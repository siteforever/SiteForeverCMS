<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Controller;

use Sfcms\Component;
use ReflectionClass;
use RuntimeException;
use Sfcms_Http_Exception;

class Resolver extends Component
{
    /** @var array */
    protected $_controllers;

    public function __construct()
    {
        $this->_controllers = $this->app()->getControllers();
    }

    /**
     * Берет данные из файла protected/controllers.php через метод $this->app()->getControllers() и на основе этого
     * конфига принимает решение о том, какой класс должен выполнять функции контроллера,
     * в каком файле и пространстве имен он находится.
     *
     * @param $controller
     * @param $action
     * @param $moduleName
     *
     * @return array
     */
    public function resolveController( $controller = null, $action = null, $moduleName = null )
    {
        $request = $this->app()->getRequest();

        if ( null === $controller ) {
            $controller = strtolower( $request->getController() );
        }
        if ( null === $action ) {
            $action = $request->getAction();
        }
        $action = strtolower( $action ) . 'Action';

        // Если не удалось определить контроллер, как известный, то инициировать ош. 404
        if ( ! isset( $this->_controllers[ $controller ] ) ) {
            $this->log(sprintf('Controller "%s" not found', $controller),__FUNCTION__);
            $controller = 'error';
            $action = 'error404Action';
            $request->setController($controller);
            $request->setAction($action);
        }

        $config = $this->_controllers[ $controller ];

        $moduleName = isset($config['module']) ? $config['module'] : $moduleName;

        if ( isset( $config['class'] ) ) {
            $controllerClass = $config['class'];
        } else {
            $controllerClass = 'Controller_' . ucfirst($controller);
        }

        if ( $moduleName ) {
            $module = $this->app()->getModule( $moduleName );
            $controllerClass = sprintf(
                '%s\\%sController',
                $module->getPath(),
                str_replace( '_', '\\', $controllerClass )
            );
        }

        return array('controller' => $controllerClass, 'action' => $action, 'module'=>$moduleName);
    }

    /**
     * Запуск контроллера
     * @param array $command
     * @return null|string
     * @throws \Sfcms_Http_Exception
     */
    public function dispatch(array $command = array())
    {
        $result = null;

        $request = $this->app()->getRequest();
        if ( 0 == count( $command ) ) {
            $command = $this->resolveController();
            if ( ! $command ) {
                throw new Sfcms_Http_Exception('Controller not resolved',404);
            }
        }

        // возможность использовать кэш
        $cache = $this->app()->getCacheManager();
        if ( $cache->isAvaible() && $cache->isCached() ) {
            $this->log('Result from cache');
            return $cache->getCache();
        }

        $this->log( $command, 'Command' );

        // если запрос является системным
        if ( $this->app()->getRouter()->isSystem() ) {
            if ( $this->app()->getAuth()->currentUser()->hasPermission( USER_ADMIN ) ) {
                $this->setSystemPage();
            } else {
                throw new Sfcms_Http_Exception( t('Access denied'), 403 );
            }
        }

        if ( ! class_exists( $command['controller'] ) ) {
            throw new Sfcms_Http_Exception( printf('Controller class "%s" not exists', $command['controller']), 404 );
        }

        $ref = new ReflectionClass($command['controller']);

        /** @var \Sfcms_Controller $controller */
        $controller = $ref->newInstance();

        // Защита системных действий
        $access = $controller->access();

//        if ( ! $ref->hasMethod($command['action'])) {
//            $command['action'] = 'indexAction';
//        }

        $this->acl( $access, $command );

        try {
            $method     = $ref->getMethod( $command[ 'action' ] );
        } catch( \ReflectionException $e ) {
            throw new Sfcms_Http_Exception($e->getMessage(),404);
        }
        $arguments  = $this->prepareArguments( $method );

        $result     = $method->invokeArgs( $controller, $arguments );

        return $result;
    }


    /**
     * Подотавливает список аргументов для передачи в Action, на основе указанных параметров и проверяет типы
     * на основе правил, указанных в DocBlock
     * @param \ReflectionMethod $method
     * @return array
     */
    protected function prepareArguments( \ReflectionMethod $method )
    {
        $arguments    = array();
        $methodParams = $method->getParameters();
        $docComment   = $method->getDocComment();
        preg_match_all( '/@param (int|float|string|array) \$([\w_-]+)/', $docComment, $match );
        foreach( $methodParams as $param ) {
            if ( $val = $this->app()->getRequest()->get($param->name) ) {
                // Фильтруем входные параметры
                $arguments[ $param->name ] = $val;
                if ( ( $key = array_search($param->name, $match[2] ) ) !== false ) {
                    switch( $match[1][$key] ) {
                        case 'int':
                            $arguments[ $param->name ] = filter_var( $val, FILTER_VALIDATE_INT );
                            break;
                        case 'float':
                            $arguments[ $param->name ] = filter_var( $val, FILTER_VALIDATE_FLOAT );
                            break;
                        case 'string':
                            $arguments[ $param->name ] = filter_var( $val, FILTER_SANITIZE_STRING );
                            break;
//                        case 'array':
//                        default:
//                            $arguments[ $param->name ] = $val;
                    }
                }
            } else {
                $arguments[ $param->name ] = $param->isOptional() ? $param->getDefaultValue() : null;
            }
        }
        return $arguments;
    }

    /**
     * Acl
     *
     * Проходит по массиву, предоставленному методом Access() контроллера
     *
     * Массив содержит в качестве ключей - группы пользователей, а в качестве значений - список методов
     * которые разрешены для этой группы
     */
    protected function acl( array $access = null, array $command = array() )
    {
        if ( $access && is_array($access) ) {
            foreach( $access as $perm => $ruleMethods ) {
                if ( 'system' == $perm ) {
                    $perm = USER_ADMIN;
                }
                $ruleMethods = is_string($ruleMethods) ? array_map( 'trim', explode(',',$ruleMethods) ) : $ruleMethods;
                if( ! is_array($ruleMethods) ) {
                    throw new RuntimeException( 'Expected string or array' );
                }
                $ruleMethods = array_map( function($method){
                    if ( false === stripos( $method, 'action' ) ) {
                        $method = strtolower( $method ) . 'Action';
                    }
                    return $method;
                }, $ruleMethods );
                if ( in_array( $command['action'], $ruleMethods ) ) {
                    if ( $this->app()->getUser()->hasPermission( $perm ) ) {
                        if ( $perm == USER_ADMIN ) {
                            $this->setSystemPage();
                        }
                    } else {
                        throw new Sfcms_Http_Exception( t('Access denied'), 403 );
                    }
                }
            }
        }
    }

    /**
     * Переводит систему на работу с админкой
     */
    private function setSystemPage()
    {
        $this->app()->getRequest()->setTemplate('index');
        $this->app()->getRequest()->set('resource', 'system:');
        $this->app()->getRequest()->set('modules', $this->app()->adminMenuModules());
    }
}

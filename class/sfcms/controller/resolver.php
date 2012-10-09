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
     * @return array
     * @throws RuntimeException
     */
    public function resolveController( $controller = null, $action = null, $module = 'default' )
    {
        $request = $this->app()->getRequest();

        if ( null === $controller ) {
            $controller = strtolower( $request->getController() );
        }
        if ( null === $action ) {
            $action = $request->getAction();
        }
        $action = strtolower( $action ) . 'Action';

        if ( ! isset( $this->_controllers[ $controller ] ) ) {
            throw new RuntimeException(sprintf('Controller %s not found in controllers config.', $controller));
        }


        $config = $this->_controllers[ $controller ];
        if ( isset( $config['class'] ) ) {
            $controllerClass = $config['class'];
        } else {
            $controllerClass = 'Controller_' . ucfirst($controller);
        }

        if ( isset( $config['module'] ) ) {
            $controllerClass = "Module\\".ucfirst(strtolower($config['module']))."\\"
                             . str_replace( '_', '\\', $controllerClass ).'Controller';
            require_once str_replace( '\\', DIRECTORY_SEPARATOR, trim( $controllerClass, '\\' ) ).'.php';
        } elseif ( isset( $config['file'] ) ) {
            require_once $config['file'];
        } else {
            require_once strtolower( str_replace(array('_','\\'), DIRECTORY_SEPARATOR, $controllerClass) ).'.php';
        }

        return array('controller' => $controllerClass, 'action' => $action);
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
            header('Status: 404 Not Found');
            $request->set('controller', 'page');
            $request->set('action', 'error404');
            $command = $this->resolveController();
        }

        $ref = new ReflectionClass($command['controller']);

        /** @var \Sfcms_Controller $controller */
        $controller = $ref->newInstance();
        $controller->init();
        // Защита системных действий
        $rules = $controller->access();

        if ( ! $ref->hasMethod($command['action'])) {
            $command['action'] = 'indexAction';
        }

        if ($rules && is_array($rules['system'])) {
            foreach ( $rules['system'] as $rule ) {
                if (strtolower($rule . 'action') == strtolower($command['action'])) {
                    if ($this->app()->getUser()->hasPermission(USER_ADMIN)) {
                        $this->setSystemPage();
                    } else {
                        throw new Sfcms_Http_Exception( t('Access denied'), 403 );
                    }
                    break;
                }
            }
        }

        $method = $ref->getMethod( $command['action'] );
        $methodParams = $method->getParameters();


        $docComment = $method->getDocComment();
        preg_match_all( '/@param (int|float|string|array) \$([\w_-]+)/', $docComment, $match );
        $arguments = array();
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


        $result = $method->invokeArgs( $controller, $arguments );
        return $result;
    }


    private function setSystemPage()
    {
        $this->app()->getRequest()->setTemplate('index');
        $this->app()->getRequest()->set('resource', 'system:');
        if ( file_exists(ROOT.'/modules.php') ) {
            $this->app()->getRequest()->set('modules', require_once(ROOT.'/modules.php'));
        } elseif ( ROOT != SF_PATH && file_exists(SF_PATH.'/modules.php') ) {
            $this->app()->getRequest()->set('modules', require_once(SF_PATH.'/modules.php'));
        }
    }
}

<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Controller;
use \Sfcms\Component;

class Resolver extends Component
{
    /**
     * @return array
     */
    public function resolveController()
    {
        $request = $this->app()->getRequest();

        $controller_class = 'Controller_' . ucfirst($request->get('controller'));
        $action = $request->get('action') . 'Action';

        return array('controller' => $controller_class, 'action' => $action);
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
        }

        // возможность использовать кэш
        if ( $this->app()->getConfig()->get('cache')
            && ! $this->app()->getRequest()->getAjax()
            && ! $this->app()->getRouter()->isSystem()
        ) {
            if( $this->app()->getAuth()->currentUser()->get('perm') == USER_GUEST ) {
                if ( ! $this->app()->getBasket()->count() ) {
                    define('CACHE', true);
                    $this->log('Cache true');
                    $cache = $this->app()->getCacheManager();
                    if ( $cache->isCached() ) {
                        $this->log('Result from cache');
                        return $cache->getCache();
                    }
                }
            }
        }
        if ( ! defined('CACHE') ) define('CACHE', false);

        if (!$command) {
            if (!$command = $this->resolveController()) {
                throw new \Sfcms_Http_Exception('Controller not resolved',404);
            }
        }
        $this->log( $command, 'Command' );
        // если запрос является системным
        if ( $this->app()->getRouter()->isSystem() ) {
            if ( $this->app()->getAuth()->currentUser()->hasPermission( USER_ADMIN ) ) {
                $this->setSystemPage();
            } else {
                throw new \Sfcms_Http_Exception( t('Access denied'), 403 );
            }
        }

        if ( ! class_exists( $command['controller'] ) ) {
            header('Status: 404 Not Found');
            $request->set('controller', 'page');
            $request->set('action', 'error404');
            $command = $this->resolveController();
        }

        $ref = new \ReflectionClass($command['controller']);

        /** @var \Sfcms_Controller $controller */
        $controller = $ref->newInstance();
        $controller->init();
        // Защита системных действий
        $rules = $controller->access();

        if ( ! $ref->hasMethod($command['action'])) {
            $command['action'] = 'indexAction';
        }

        if ($rules && is_array($rules['system'])) {
            foreach ($rules['system'] as $rule) {
                if (strtolower($rule . 'action') == strtolower($command['action'])) {
                    if ($this->app()->getUser()->hasPermission(USER_ADMIN)) {
                        $this->setSystemPage();
                    } else {
                        throw new \Sfcms_Http_Exception( t('Access denied'), 403 );
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
        $this->app()->getRequest()->set('template', 'index');
        $this->app()->getRequest()->set('resource', 'system:');
        $this->app()->getRequest()->set('modules', @include_once('modules.php'));
    }
}

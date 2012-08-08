<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class ControllerResolver extends \Sfcms\Component
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
     * @throws Sfcms_Http_Exception
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
                throw new Sfcms_Http_Exception('Controller not resolved',404);
            }
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

        /** @var Sfcms_Controller $controller */
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
                        throw new Sfcms_Http_Exception( t('Access denied'), 403 );
                    }
                    break;
                }
            }
        }
        $result = $controller->{$command['action']}();
        return $result;
    }


    private function setSystemPage()
    {
        $this->app()->getRequest()->set('template', 'index');
        $this->app()->getRequest()->set('resource', 'system:');
        $this->app()->getRequest()->set('modules', @include('modules.php'));
    }
}

<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class ControllerResolver
{
    /**
     * @var Application_Abstract
     */
    private $app;

    public function __construct(Application_Abstract $app)
    {
        $this->app = $app;
    }

    /**
     * @return array
     */
    public function resolveController()
    {
        $request = $this->app->getRequest();

        $controller_class = 'Controller_' . ucfirst($request->get('controller'));
        $action = $request->get('action') . 'Action';

        return array('controller' => $controller_class, 'action' => $action);
    }

    /**
     * Запуск контроллера
     * @params array $command
     * @throws ControllerException
     * @return mixed
     */
    public function dispatch(array $command = array())
    {
        $result = null;

        // возможность использовать кэш
        if( $this->app->getConfig()->get('cache') && ! $this->app->getRequest()->getAjax() && ! $this->app->getRouter()->isSystem()) {
            if( $this->app->getAuth()->currentUser()->get('perm') == USER_GUEST ) {
                if ( ! $this->app->getBasket()->count() ) {
                    define('CACHE', true);
                    App::$DEBUG && $this->app->getLogger()->log('Cache true');
                    $cache = $this->app->getCacheManager();
                    if ( $cache->isCached() ) {
                        App::$DEBUG && $this->app->getLogger()->log('Result from cache');
                        return $cache->getCache();
                    }
                }
            }
        }
        if ( ! defined('CACHE') ) define('CACHE', false);

        if (!$command) {
            if (!$command = $this->resolveController()) {
                throw new ControllerException('Controller not resolved');
            }
        }
        if ( $this->app->getConfig()->get('debug.profile') ) {
            $this->app->getLogger()->log( $command, 'Command' );
        }
        // если запрос является системным
        if ( $this->app->getRouter()->isSystem() ) {
            if ( $this->app->getAuth()->currentUser()->hasPermission( USER_ADMIN ) ) {
                $this->setSystemPage();
            } else {
                $this->setProtectedPage();
            }
        }

        $ref = new ReflectionClass($command['controller']);

        /** @var Sfcms_Controller $controller */
        $controller = $ref->newInstance( $this->app );
        $controller->init();
        // Защита системных действий
        $rules = $controller->access();

        if ($rules && is_array($rules['system'])) {
            foreach ($rules['system'] as $rule) {
                if (strtolower($rule . 'action') == strtolower($command['action'])) {
                    if ($this->app->getUser()->hasPermission(USER_ADMIN)) {
                        $this->setSystemPage();
                    } else {
                        $this->setProtectedPage();
                    }
                    break;
                }
            }
        }
        if ($ref->hasMethod($command['action'])) {
            $result = $controller->{$command['action']}();
        } else {
            $result = $controller->indexAction();
        }
        return $result;
    }


    private function setSystemPage()
    {
        $this->app->getRequest()->set('template', 'index');
        $this->app->getRequest()->set('resource', 'system:');
        $this->app->getRequest()->set('modules', @include('modules.php'));
    }

    private function setProtectedPage()
    {
        $this->app->getRequest()->addFeedback(t('Protected page'));
        $this->app->getRequest()->set('controller', 'users');
        $this->app->getRequest()->set('action', 'login');
        $this->app->getRequest()->setTitle(t('Access denied'));
        throw new ControllerException(t('Access denied'));
    }
}

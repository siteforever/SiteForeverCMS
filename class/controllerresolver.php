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

    function __construct( Application_Abstract $app )
    {
        $this->app  = $app;
    }

    /**
     * @return array
     */
    function resolveController()
    {
        $request    = $this->app->getRequest();

        $controller_class   = 'Controller_'.ucfirst($request->get('controller'));
        $action             = $request->get('action').'Action';

        return array( 'controller' => $controller_class, 'action' => $action );
    }

    /**
     * Запуск контроллера
     * @params array $command
     * @throws ControllerExeption
     * @return mixed
     */
    function dispatch( array $command = array() )
    {
        $result = null;
        if ( ! $command ) {
            if ( ! $command = $this->resolveController() ) {
                throw new ControllerException('Controller not resolved');
            }
        }
//        printVar($command);


        // если запрос является системным
        if ( $this->app->getRouter()->isSystem() ) {
            if ( $this->app->getAuth()->currentUser()->hasPermission( USER_ADMIN ) ) {
                $this->setSystemPage();
            } else {
                $this->setProtectedPage();
            }
        }

        if ( class_exists( $command['controller'] ) )
        {
            $ref    = new ReflectionClass( $command['controller'] );
            /**
             * @var Controller $controller
             */
            $controller     = $ref->newInstance( $this->app );

            if ( $ref->hasMethod( 'init' ) ) {
                $controller->init();
            }

            // Защита системных действий
            $rules  = $controller->access();

            if ( $rules && is_array($rules['system']) ) {
                foreach ( $rules['system'] as $rule ) {
                    if ( strtolower( $rule.'action' ) == strtolower( $command['action'] ) ) {
                        if ( $this->app->getUser()->hasPermission( USER_ADMIN ) ) {
                            $this->setSystemPage();
                        } else {
                            $this->setProtectedPage();
                        }
                        break;
                    }
                }
            }
            if ( $ref->hasMethod( $command['action'] ) ) {
//                printVar($command);
                $result = $controller->$command['action']();
            }
            else {
                throw new ControllerException(t('Could not start the controller').' '.$command['controller'] . ':' . $command['action'] . '()');
            }
        }
        else {
            throw new ControllerException(t('Unable to find controller').' '.$command['controller']);
        }
        return $result;
    }


    private function setSystemPage()
    {
        $this->app->getRequest()->set('template', 'index' );
        $this->app->getRequest()->set('resource', 'system:');
        $this->app->getRequest()->set('modules', @include('modules.php'));
    }

    private function setProtectedPage()
    {
        $this->app->getRequest()->addFeedback( t('Protected page') );
        $this->app->getRequest()->set('controller', 'users');
        $this->app->getRequest()->set('action', 'login');
        throw new ControllerException(t('Access denied'));
    }
}

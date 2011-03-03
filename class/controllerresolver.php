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

    function resolveController()
    {
        $request    = $this->app->getRequest();

        $controller_class   = 'Controller_'.ucfirst($request->get('controller'));
        $action             = $request->get('action').'Action';

        return array( 'controller' => $controller_class, 'action' => $action );
    }

    /**
     * Запуск контроллера
     * @throws ControllerExeption
     * @return void
     */
    function callController( $command = array() )
    {
        if ( ! $command ) {
            if ( ! $command = $this->resolveController() ) {
                throw new ControllerException('Controller not resolved');
            }
        }
        //printVar($command);

        if ( class_exists( $command['controller'] ) )
        {
            $ref    = new ReflectionClass( $command['controller'] );
            /**
             * @var Controller $controller
             */
            $controller     = $ref->newInstance( $this->app );

            //$controller = new $command['controller']( $this->app );
            
            if ( $ref->hasMethod( 'init' ) ) {
                $controller->init();
            }


            // Защита системных действий
            $rules  = $controller->access();

            if ( ! $this->app->getUser()->hasPermission( USER_ADMIN ) ) {
                if ( isset( $rules['system'] ) && is_array( $rules['system'] ) ) {
                    foreach ( $rules['system'] as $rule ) {
                        if ( strtolower( $rule.'action' ) == strtolower( $command['action'] ) ) {
                            throw new ControllerException(t('Access denied'));
                        }
                    }
                }
            }



            if ( $ref->hasMethod( $command['action'] ) ) {
                $return = $controller->$command['action']();
            }
            elseif ( $ref->hasMethod( 'indexAction' ) ) {
                $return = $controller->indexAction();
                $controller->deInit();
            }
            else {
                throw new ControllerException(t('Could not start the controller').' '.$command['controller']);
            }
        }
        else {
            throw new ControllerException(t('Unable to find controller').' '.$command['controller']);
        }
        return $return;
    }
}

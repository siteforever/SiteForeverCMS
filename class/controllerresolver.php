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

        $controller_class   = 'controller_'.ucfirst($request->get('controller'));
        $action             = $request->get('action').'Action';

        return array( 'controller' => $controller_class, 'action' => $action );
    }

    /**
     * Запуск контроллера
     * @throws ControllerExeption
     * @return void
     */
    function callController()
    {
        if ( ! $command = $this->resolveController() ) {
            throw new Application_Exception('Controller not resolved');
        }

        if ( class_exists( $command['controller'] ) )
        {
            $reflection_class = new ReflectionClass( $command['controller'] );
            //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
            /**
             * @var Controller $controller
             */
            $controller = new $command['controller']( $this->app );
            //print $controller_class.'::'.$action;
            //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
            if ( $reflection_class->hasMethod( 'init' ) ) {
                $controller->init();
            }

            if ( $reflection_class->hasMethod( $command['action'] ) ) {
                $return = $controller->$command['action']();
            }
            elseif ( $reflection_class->hasMethod( 'indexAction' ) ) {
                $return = $controller->indexAction();
                $controller->deInit();
            }
            else {
                throw new ControllerExeption(t('Could not start the controller').' '.$command['controller']);
            }
        }
        else {
            throw new ControllerExeption(t('Unable to find controller').' '.$command['controller']);
        }
        return $return;
    }
}

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
     * Запуск контроллера
     * @throws ControllerExeption
     * @return void
     */
    function callController()
    {
        $request    = $this->app->getRequest();


        $controller_class   = 'controller_'.ucfirst($request->get('controller'));
        $action             = $request->get('action').'Action';

        if ( class_exists( $controller_class ) )
        {
            $reflection_class = new ReflectionClass( $controller_class );
            //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
            /**
             * @var Controller $controller
             */
            $controller = new $controller_class( $this->app );
            //print $controller_class.'::'.$action;
            //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
            if ( $reflection_class->hasMethod( 'init' ) ) {
                $controller->init();
            }

            if ( $reflection_class->hasMethod( $action ) ) {
                $return = $controller->$action();
            }
            elseif ( $reflection_class->hasMethod( 'indexAction' ) ) {
                $return = $controller->indexAction();
                $controller->deInit();
            }
            else {
                throw new ControllerExeption(t('Could not start the controller').' '.$controller_class);
            }
        }
        else {
            throw new ControllerExeption(t('Unable to find controller').' '.$controller_class);
        }
        return $return;
    }
}

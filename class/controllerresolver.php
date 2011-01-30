<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class ControllerResolver
{
    function callController( Request $request )
    {
        $controller_class   = 'controller_'.$request->get('controller');
        $action             = $request->get('action').'Action';

        if ( class_exists( $controller_class ) )
        {
            $reflection_class = new ReflectionClass( $controller_class );

            /**
             * @var Controller $controller
             */
            $controller = new $controller_class();

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

<?php
/**
 * Команда
 * Вызывает действие указанного контроллера
 *
 * {command name="page"}
 * {command controller="page"}
 * {command name="admin" action="add"}
 * {command controller="admin" action="add"}
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
function smarty_function_command( $params, $smarty )
{
    $app    = App::getInstance();

    if ( ! isset( $params['controller'] ) && ! isset( $params['name'] ) ) {
        throw new Application_Exception();
    }

    if ( isset( $params['name'] ) ) {
        $controller = 'Controller_'.$params['name'];
    } else {
        $controller = 'Controller_'.$params['controller'];
    }

    $action = ( isset( $params['action'] ) ) ? $params['action'] : 'index';

    $action = strtolower( $action ).'Action';


    if ( class_exists( $controller ) ) {

        $command    = new $controller( $app );

        if ( $command instanceof Controller ) {
            $command->$action();
        }


        return $app->getRequest()->getContent();
    }
}
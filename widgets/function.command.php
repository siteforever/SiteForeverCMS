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
 
function smarty_function_command( $params )
{
    $app    = App::getInstance();

    if ( ! isset( $params['controller'] ) && ! isset( $params['name'] ) ) {
        throw new Exception();
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

        if ( $command instanceof Sfcms_Controller && method_exists( $command, $action ) ) {
            call_user_func(array($command, $action));
        }

        return $app->getRequest()->getContent();
    }
    else {
        return 'command not exists';
    }
}
<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.alert.php
* Type:     function
* Name:     alert
* Purpose:  Сделает алерт в стиле TwBootstrap
* -------------------------------------------------------------
*/
function smarty_function_alert($params, Smarty_Internal_Template $template)
{
    return sprintf(
        '<div class="alert%s">%s</div>',
        isset( $params['type'] ) ? ' alert-'.$params['type'] : '',
        isset( $params['msg'] ) ? $params['msg'] : ''
    );
}
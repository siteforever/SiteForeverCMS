<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.menu.php
* Type:     function
* Name:     menu
* Purpose:  Выведет меню на сайте
* -------------------------------------------------------------
*/
function smarty_function_menu($params, $smarty)
{
    $parent = isset( $params['parent'] ) ? $params['parent'] : '0';
    $level  = isset( $params['level'] ) ? $params['level'] : '1';

    return Model::getModel('Structure')->getMenu( $parent, $level );
}

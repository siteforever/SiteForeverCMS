<?php
/*
 * Smarty plugin
 *
 *
 * -------------------------------------------------------------
 * File:     function.icon.php
 * Type:     function
 * Name:     menu
 * Purpose:  Выведет иконку на сайте
 * -------------------------------------------------------------
 *
 * @example {icon name="accept" title="Принято"}
 *
 */
function smarty_function_icon($params, $smarty)
{
    if ( !isset($params['name']) ) {
        return '"name" param required';
    }
    $name = $params['name'];
    if (isset($params['title'])) {
        $title = $params['title'];
    } else {
        $title = $name;
    }
    return Sfcms::html()->icon($name, $title);
}

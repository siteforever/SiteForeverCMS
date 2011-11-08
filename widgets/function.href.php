<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.href.php
 * Type:     function
 * Name:     href
 * Purpose:  Выдаст href-параметр для ссылки
 * -------------------------------------------------------------
 * @example {href url="about/contacts"}             => href="/about/contacts"
 * @example {href url="about/contacts" "page"="2"}  => href="/about/contacts/page=2"
 * @example {href "page"="2"} // using current url  => href="/current/url/path/page=2"
 */
function smarty_function_href( $params )
{
    $url    = $params['url'];
//    if ( empty($params['url']) ) {
//        $url = null;
//    } else {
//        $url = $params['url'];
//    }
    unset($params['url']);

//    if ( null == $url && isset($params['controller']) && isset($params['action']) ) {
//        $controller = $params['controller'];
//        $action     = $params['action'];
//        unset($params['controller']);
//        unset($params['action']);
//        return App::getInstance()->getRouter()->createServiceLink( $controller, $action, $params );
//    }

    return Siteforever::html()->href( $url, $params );
    //return href( $url, $params );
}
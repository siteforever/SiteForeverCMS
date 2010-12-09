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
    if ( empty($params['url']) ) {
        $url = '';
    } else {
        $url = $params['url'];
        unset($params['url']);
    }
    return href( $url, $params );
}
<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.link.php
 * Type:     function
 * Name:     link
 * Purpose:  Выдаст ссылку
 * -------------------------------------------------------------
 * @example {link url="about/contacts"}             => /about/contacts
 * @example {link url="about/contacts" "page"="2"}  => /about/contacts/page=2
 * @example {link "page"="2"} // using current url  => /current/url/path/page=2
 */
function smarty_function_link( $params )
{
    if ( empty($params['url']) ) {
        $url = '';
    } else {
        $url = $params['url'];
        unset($params['url']);
    }
    return App::getInstance()->getRouter()->createLink( $url, $params );
}

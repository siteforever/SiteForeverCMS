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
function smarty_function_href($params)
{
    $url = isset($params['url']) ? $params['url'] : null;
    unset($params['url']);

    return Sfcms::html()->href($url, $params);
}
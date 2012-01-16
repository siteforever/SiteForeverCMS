<?php
/**
 * Гугл плюс
 * @param array $params
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 * @return string
 */
function smarty_function_gplus( $params )
{
    $protocol   = isset($_SERVER['SSH']) ? 'https' : 'http' . '://';
    $domain     = $_SERVER['HTTP_HOST'];
    $addr       = $_SERVER['REQUEST_URI'];

    $dataSize   = isset($params['data-size']) ? $params['data-size'] : 'medium';

    $url    = $protocol . $domain . $addr;

    return  "<div class=\"g-plusone\" data-size=\"{$dataSize}\" data-annotation=\"inline\" data-href=\"{$url}\"></div>"
            . "<script type=\"text/javascript\">"
            . "window.___gcfg = {lang: 'ru'};"
            . "(function() {"
                . "var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;"
                . "po.src = 'https://apis.google.com/js/plusone.js';"
                . "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);"
            . "})();"
            . "</script>";
}
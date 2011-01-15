<?php
/**
 * Хэлперы HTML
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Siteforever_Html
{
    static private $counter = 0;

    /**
     * Позволяет создать только 1 экземпляр
     * @throws Siteforever_Exception
     */
    function __construct() {
        self::$counter ++;
        if ( self::$counter > 1 ) {
            throw new Siteforever_Exception('HTML class singleton');
        }
    }

    /**
     * Содаст HTML ссылку
     * @param  $text
     * @param  $url
     * @param array $params
     * @return string
     */
    function link( $text, $url, $params = array() )
    {
        $href   = $this->href( $url, $params );
        return "<a {$href}>{$text}</a>";
    }


    /**
     * Создаст ссылку
     * @param string $url
     * @param array  $params
     */
    function href( $url = '', $params = array() )
    {
        return 'href="'.App::$router->createLink( $url, $params ).'"';
    }
}

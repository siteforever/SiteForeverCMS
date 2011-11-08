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
    public function __construct() {
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
    public function link( $text, $url, $params = array() )
    {
        return '<a '.$this->href( $url, $params ).'>'.$text.'</a>';
    }

    /**
     * Создаст ссылку
     * @param string $url
     * @param array  $params
     * @return string
     */
    public function href( $url = '', $params = array() )
    {
//        var_dump( $url, $params );
        return 'href="'.App::getInstance()->getRouter()->createLink( $url, $params ).'"';
    }
}

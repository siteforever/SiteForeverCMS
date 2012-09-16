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
     * Вернет строку URL для указанных параметров
     * @param string|null $url
     * @param array $params
     * @return string
     */
    public function url( $url, $params = array() )
    {
        return App::getInstance()->getRouter()->createLink( $url, $params );
    }

    /**
     * Содаст HTML ссылку
     * @param  $text
     * @param  $url
     * @param array $params
     * @return string
     */
    public function link( $text, $url = "#", $params = array(), $class = "" )
    {
        $attributes = array();
        if ( $class ) {
            $params['class'] = $class;
        }
        if ( isset( $params['nofollow'] ) ) {
            if ( $params['nofollow'] ) {
                $attributes[] = "rel=\"nofollow\"";
                unset( $params['rel'] );
            }
            unset( $params['nofollow'] );
        }
        $passAttrs = array('class','title','rel');
        foreach ( $passAttrs as $attr ) {
            if ( isset( $params[$attr] ) ) {
                $attributes[] = "{$attr}=\"{$params[$attr]}\"";
                unset( $params[$attr] );
            }
        }
        foreach ( $params as $key => $val ) {
            if ( 'html' == substr( $key, 0, 4 ) ) {
                unset( $params[$key] );
                $key = strtolower( substr( $key, 4 ) );
                $attributes[] = "{$key}=\"{$val}\"";
                continue;
            }
            if ( 'data' == substr( $key, 0, 4 ) ) {
                $attributes[] = "{$key}=\"{$val}\"";
            }
        }
        if ( isset( $params['controller'] ) && '#' == $url ) {
            $url = null;
        }
        $attributes[] = $this->href( $url, $params );
        return '<a '.implode(' ', $attributes).'>'.$text.'</a>';
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
        return 'href="'.$this->url( $url, $params ).'"';
    }
}

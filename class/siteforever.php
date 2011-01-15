<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Siteforever
{
    static $instance;

    static protected $html = null;

    private function __construct() {}

    /**
     * @static
     * @return Siteforever
     */
    static function getInstance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Siteforever();
        }
    }

    /**
     * HTML Helper
     * @static
     * @return Siteforever_Html
     */
    static function html()
    {
        if ( is_null( self::$html ) ) {
            self::$html = new Siteforever_Html();
        }
        return self::$html;
    }

}

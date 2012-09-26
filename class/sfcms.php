<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

use Sfcms\Html;
 
class Sfcms
{
    static $instance;

    static protected $html = null;

    private function __construct() {}

    /**
     * @static
     * @return Sfcms
     */
    static function getInstance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Sfcms();
        }
    }

    /**
     * HTML Helper
     * @static
     * @return Html
     */
    static function html()
    {
        if ( is_null( self::$html ) ) {
            self::$html = new Html();
        }
        return self::$html;
    }

}

<?php
/**
 * Логгер ChromePHP
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Std_Logger_Chrome implements Std_Logger_Logger
{

    public function __construct()
    {
//        ChromePhp::useFile( ROOT . '/tmp/chrome.log', '/tmp/chrome.log' );
    }

    public function log( $message, $label = '' )
    {
        if ( $label ) {
            return ChromePhp::log( $label, $message );
        }
        else {
            return ChromePhp::log( $message );
        }
    }

    public function dump()
    {
        ChromePhp::log( print_r( func_get_args(), 1 ) );
    }
}

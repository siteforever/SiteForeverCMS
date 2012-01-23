<?php
/**
 * Логгер ChromePHP
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

require_once 'ChromePhp.php';

class std_logger_chrome implements std_logger_logger
{

    public function __construct()
    {
        ChromePhp::useFile( ROOT . '/tmp/chrome.log', '/tmp/chrome.log' );
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
}

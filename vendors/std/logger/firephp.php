<?php
/**
 * Логгер FirePHP
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

require_once 'vendors/FirePHPCore/FirePHP.class.php';
 
class std_logger_firephp implements std_logger_logger
{
    /**
     * @param $message
     * @param string $label
     * @return true
     */
    public function log( $message, $label = '' )
    {
        return FirePHP::getInstance(true)->log($message, $label);
    }

    /**
     * @return true
     */
    public function dump( )
    {
        return FirePHP::getInstance(true)->dump('sfcms', func_get_args() );
    }
}

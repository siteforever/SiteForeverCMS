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
    public function log( $message, $label = '' )
    {
        return FirePHP::getInstance(true)->log($message, $label);
    }
}

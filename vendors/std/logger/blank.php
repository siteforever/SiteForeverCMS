<?php
/**
 * Пустой логгер
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class std_logger_blank implements std_logger_logger
{
    public function log( $message, $label = '' )
    {
    }

    public function dump()
    {
    }
}

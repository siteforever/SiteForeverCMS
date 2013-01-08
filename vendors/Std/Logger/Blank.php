<?php
/**
 * Пустой логгер
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Std_Logger_Blank implements Std_Logger_Logger
{
    public function log( $message, $label = '' )
    {
    }

    public function dump()
    {
    }
}

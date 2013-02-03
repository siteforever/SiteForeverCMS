<?php
/**
 * Текстовый логгер
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Std_Logger_Plain implements Std_Logger_Logger
{
    public function log( $message, $label = '' )
    {
        print "<pre>{$message}</pre>\n";
    }

    public function dump()
    {
    }

}

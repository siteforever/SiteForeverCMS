<?php
/**
 * Интерфейс логгеров
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

interface Std_Logger_Logger
{
    public function log( $message, $label = '' );

    public function dump( );
}

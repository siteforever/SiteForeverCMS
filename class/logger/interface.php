<?php
/**
 * Интерфейс логера
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

interface Logger_Interface
{
    public function log( $message, $label = '' );
}

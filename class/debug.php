<?php
/**
 * Обертка для отладчика
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class debug
{
    /**
     * @static
     * @param $msg
     * @param string $label
     */
    static public function log( $msg, $label = '' )
    {
        App::getInstance()->getLogger()->log( $msg, $label );
    }

    /**
     * @static
     * @param $var
     */
    static public function dump( $var )
    {
        printVar( $var );
    }
}

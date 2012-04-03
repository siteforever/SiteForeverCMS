<?php
/**
 * Пишет логи в файл
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class std_logger_file implements std_logger_logger
{
    private $_file  = 'tmp/error.log';

    private $_log   = array();

    public function log( $message, $label = '' )
    {
        $this->_log[]   = $message;
    }

    function __destruct()
    {
        file_put_contents( $this->_file, join("\n", $this->_log) );
    }

    public function dump()
    {
    }
}

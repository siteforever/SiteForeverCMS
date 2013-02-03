<?php
/**
 * Пишет логи в файл
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Std_Logger_File implements Std_Logger_Logger
{
    private $_file  = '/logger.txt';

    private $_log   = array();

    public function __construct()
    {
        file_put_contents(
            ROOT . $this->_file,
            "\n\n\n\n\n==============".strftime("%d-%m-%Y %H:%M")."=============\n\n\n\n\n",
            FILE_APPEND
        );
    }

    public function log( $message, $label = '' )
    {
        $this->_log[ $label ]   = $message;
        file_put_contents(
            ROOT . $this->_file,
            sprintf('%s: %s', $label, var_export($message, 1))."\n",
            FILE_APPEND
        );
    }

    public function __destruct()
    {
    }

    public function dump()
    {
    }
}

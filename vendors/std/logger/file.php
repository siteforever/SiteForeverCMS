<?php
/**
 * Пишет логи в файл
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class std_logger_file implements std_logger_logger
{
    private $_file  = '/logger.txt';

    private $_log   = array();

    public function log( $message, $label = '' )
    {
        $this->_log[ $label ]   = $message;
    }

    public function __destruct()
    {
        $output = array();
        foreach( $this->_log as $label => $msg ) {
            $output[] = "{$label}: ".var_export($msg, 1);
        }
        $output[] = "==============".strftime("%d-%m-%Y %H:%M")."=============\n\n\n";
        file_put_contents( ROOT . $this->_file, iconv('utf-8', 'cp866', join("\n", $output ) ), FILE_APPEND );
    }

    public function dump()
    {
    }
}

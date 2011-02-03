<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Logger_Plain implements Logger_Interface
{
    protected $list_log = array();

    public function log($message, $label = '')
    {
        $this->list_log[]  = $label.':'.$message;
    }

    function __destruct()
    {
        print join("\n", $this->list_log)."\n\n";
    }
}

<?php
/**
 * Логгер фаербагом
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

class Logger_Firephp extends Logger
{
    function init()
    {
        $this->logger = Logger_Adapter_Firephp::getInstance(true);
    }
}

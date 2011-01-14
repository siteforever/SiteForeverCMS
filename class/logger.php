<?php
/**
 * Логирование данных
 * Переадресует log() на экземпляр $logger
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

abstract class Logger implements Logger_Interface
{
    /**
     * @var Logger_Interface
     */
    protected $logger;

    function __construct()
    {
        $this->init();
    }

    abstract function init();

    function log( $message, $label = '' )
    {
        $this->logger->log( $message, $label );
    }
}

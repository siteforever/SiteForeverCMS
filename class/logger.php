<?php
/**
 * Логирование данных
 * @autor keltanas
 */
 
class logger
{
    private $logger;

    function __construct()
    {
        $this->logger = FirePHP::getInstance(true);
    }

    function log( $message, $label = '' )
    {
        $this->logger->log( $message, $label );
    }
}

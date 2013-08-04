<?php
/**
 * Monolog Adapter
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Monolog\Logger;

use Sfcms\LoggerInterface;

class Logger implements LoggerInterface
{
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param      $message
     * @param null $label
     *
     * @return mixed|void
     */
    public function log($message, $label = null)
    {
        if (is_array($message)) {
            $message = join(", ", $message);
        }
        if ($label) {
            $message = $label . ': ' . $message;
        }
        $this->logger->addInfo($message);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function debug($message, array $context = array())
    {
        return $this->logger->addDebug($message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function info($message, array $context = array())
    {
        return $this->logger->addInfo($message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function warning($message, array $context = array())
    {
        return $this->logger->addWarning($message, $context);
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function error($message, array $context = array())
    {
        return $this->logger->addError($message, $context);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function critical($message, array $context = array())
    {
        return $this->logger->addCritical($message, $context);
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function alert($message, array $context = array())
    {
        return $this->logger->addAlert($message, $context);
    }
}

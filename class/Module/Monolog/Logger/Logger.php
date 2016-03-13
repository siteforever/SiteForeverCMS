<?php
/**
 * Monolog Adapter
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Monolog\Logger;

use Sfcms\LoggerInterface;

class Logger implements LoggerInterface
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param      $message
     * @param null $label
     * @param array $context
     *
     * @return mixed|void
     */
    public function log($message, $label = null, array $context = [])
    {
        if (is_array($message)) {
            $message = join(", ", $message);
        }
        if ($label) {
            $message = $label . ': ' . $message;
        }
        $this->logger->debug($message);
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
        return $this->logger->debug($message, $context);
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
        return $this->logger->info($message, $context);
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
        return $this->logger->warning($message, $context);
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
        return $this->logger->error($message, $context);
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
        return $this->logger->critical($message, $context);
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
        return $this->logger->alert($message, $context);
    }
}

<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Doctrine\Logger;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;

class DoctrineLogger implements SQLLogger
{
    /** @var LoggerInterface */
    private $logger;

    private $sql;
    private $time;
    private $params;

    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string $sql The SQL to be executed.
     * @param array|null $params The SQL parameters.
     * @param array|null $types The SQL parameter types.
     *
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->sql = $sql;
        $this->params = $params;
        $this->time = microtime(1);
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
        $this->logger->debug($this->sql, ['params'=>$this->params, 'time'=>round(microtime(1) - $this->time, 3)]);
    }
}

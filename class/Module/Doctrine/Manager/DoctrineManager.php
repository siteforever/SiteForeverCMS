<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Doctrine\Manager;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;

class DoctrineManager
{
    /** @var \PDO */
    protected $pdo;

    /** @var Configuration */
    protected $configuration;

    /** @var EventManager */
    protected $eventManager;

    public function __construct(Configuration $configuration, EventManager $eventManager, \PDO $pdo)
    {
        $this->configuration = $configuration;
        $this->eventManager = $eventManager;
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}

<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Doctrine\Manager;


use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

class DoctrineManager
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var \PDO */
    protected $pdo;

    /** @var Configuration */
    protected $configuration;

    /** @var EventManager */
    protected $eventManager;

    function __construct(Configuration $configuration, EventManager $eventManager, \PDO $pdo)
    {
        $this->configuration = $configuration;
        $this->eventManager = $eventManager;
        $this->pdo = $pdo;
    }

    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->createEntityManager();
        }

        return $this->entityManager;
    }

    protected function createEntityManager()
    {
        return EntityManager::create(['pdo'=>$this->pdo], $this->configuration, $this->eventManager);
    }
}

<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Data;

use Sfcms\db;
use Sfcms\Model;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DataManager
{
    /** @var array */
    private $modelList = null;

    /** @var db */
    private $db;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(db $db, EventDispatcherInterface $eventDispatcher, array $modelList)
    {
        $this->db = $db;
        $this->eventDispatcher = $eventDispatcher;
        $this->modelList = $modelList;
    }

    /**
     * @param $modelId
     * @return Model
     */
    protected function createModel($modelId)
    {
        if (!isset($this->modelList[$modelId]['object'])) {
            $className = $this->modelList[$modelId]['class'];
            $this->modelList[$modelId]['object'] = new $className($this);
        }
        return $this->modelList[$modelId]['object'];
    }

    /**
     * Get appropriate model class
     *
     * @param string $model
     *
     * @return Model
     * @throws \RuntimeException
     */
    public function getModel($model)
    {
        $model = strtolower($model);
        if (false !== strpos($model, '.')) {
            if ('mapper' !== substr($model, 0, 6)) {
                $model = 'mapper.' . $model;
            }
            if (isset($this->modelList[$model])) {
                return $this->createModel($model);
            }
        }

        $models = $this->modelList;
        foreach ($models as $cfg) {
            if ($model == strtolower($cfg['alias'])) {
                return $this->createModel($cfg['id']);
            }
        }

        throw new \RuntimeException(sprintf(
            'Model "%s" not found. Available values (%s)',
            $model, implode(', ', array_column($models, 'alias'))
        ));
    }

    /**
     * @return array
     */
    public function getModelList()
    {
        return $this->modelList;
    }

    /**
     * @param string $moduleName
     * @param string $alias
     *
     * @return string
     */
    public static function getModelId($moduleName, $alias)
    {
        return sprintf('Mapper.%s.%s', $moduleName, $alias);
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return db
     */
    public function getDB()
    {
        return $this->db;
    }
}

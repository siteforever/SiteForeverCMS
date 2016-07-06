<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Data;

use Sfcms\Model;
use Sfcms\Module;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DataManager
{
    use ContainerAwareTrait;

    /** @var array */
    private $modelList = null;

    function __construct($modelList)
    {
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
            $model, join(', ', array_map(function($m){ return $m['alias']; }, $models))
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
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return \Sfcms\Db\
     */
    public function getDB()
    {
        return $this->container->get('db');
    }
}

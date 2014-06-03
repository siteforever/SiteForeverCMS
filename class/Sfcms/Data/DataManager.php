<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Data;

use Sfcms\Kernel\AbstractKernel;
use Sfcms\Model;
use Sfcms\Module;
use Symfony\Component\DependencyInjection\ContainerAware;

class DataManager extends ContainerAware
{
    /** @var array */
    private $modelList = null;

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
            if ('mapper' === strtolower(substr($model, 0, 6))) {
                return $this->container->get($model);
            } else {
                return $this->container->get('mapper.' . $model);
            }
        }

        $models = $this->getModelList();
        foreach ($models as $cfg) {
            if ($model == strtolower($cfg['alias'])) {
                return $this->container->get($cfg['id']);
            }
        }

        throw new \RuntimeException(sprintf(
            'Model "%s" not found. Available values (%s)',
            $model, join(', ', array_map(function($m){ return $m['alias']; }, $models))
        ));
    }

    /**
     * @param Module $module
     * @param string $alias
     *
     * @return string
     */
    public function getModelId(Module $module, $alias)
    {
        return sprintf('Mapper.%s.%s', $module->getName(), $alias);
    }

    /**
     * @return array
     */
    public function getModelList()
    {
        if (null === $this->modelList) {
            /** @var AbstractKernel $kernel */
            $kernel = $this->container->get('kernel');

            $modules = $kernel->getModules();
            $models = array();

            /** @var Module $module */
            foreach($modules as $module) {
                $config = $module->config();
                if ($config && isset($config['models'])) {
                    foreach($config['models'] as $alias => $className) {
                        $models[] = array(
                            'id' => $this->getModelId($module, $alias),
                            'alias' => $alias,
                            'class' => $className,
                        );
                    }
                }
            }

            $this->modelList = $models;
        }

        return $this->modelList;
    }
}

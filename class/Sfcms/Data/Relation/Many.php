<?php
/**
 * Отношение "Содержит несколько"
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Sfcms\Data\Relation;

use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Data\Relation;

class Many extends Relation
{
    static protected $_cache = array();

    public function with(Collection $collection, $rel)
    {
        /** @var $obj Object */
        $keys = array_map(function($obj) use ($rel) {
            return $obj->getId();
        }, iterator_to_array($collection));

        if (count($keys)) {
            try {
                $cond = $this->prepareCond($keys);
            } catch (Exception $e) {
                return;
            }
            if (isset($this->relation['with'])) {
                $this->model->with($this->relation['with']);
            }
            $objects = $this->model->findAll($cond);
            $cache = array();
            foreach ($objects as $obj) {
                if (!isset($cache[$obj->{$this->key}])) {
                    $cache[$obj->{$this->key}] = new Collection();
                }
                $cache[$obj->{$this->key}]->add($obj);
            }
            foreach ($collection as $item) {
                if (isset($cache[$item->id])) {
                    $item->$rel = $cache[$item->id];
                } else {
                    $item->$rel = new Collection();
                }
            }
        }
    }

    /**
     * @return Collection
     */
    public function find()
    {
        if (null === $this->getCache($this->obj->getId())) {
            if (isset($this->relation['with'])) {
                $this->model->with($this->relation['with']);
            }
            try {
                $cond = $this->prepareCond($this->obj->getId());
            } catch(Exception $e) {
                return null;
            }
            $this->setCache($this->obj->getId(), $this->model->findAll($cond));
        }

        return $this->getCache($this->obj->getId());
    }

    /**
     * @param $id
     * @param $collection
     */
    protected function setCache($id, Collection $collection)
    {
        self::$_cache[$this->getModelName()][$this->key][$id] = $collection;
    }

    /**
     * @param $id
     * @param $obj
     */
    protected function addCache($id, Object $obj)
    {
        if (empty(self::$_cache[$this->getModelName()][$this->key][$id])) {
            self::$_cache[$this->getModelName()][$this->key][$id] = new Collection();
        }
        self::$_cache[$this->getModelName()][$this->key][$id]->add($obj);
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function getCache($id)
    {
        if (isset(self::$_cache[$this->getModelName()][$this->key][$id])) {
            return self::$_cache[$this->getModelName()][$this->key][$id];
        }
        return null;
    }
}

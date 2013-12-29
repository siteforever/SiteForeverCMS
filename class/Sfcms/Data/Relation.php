<?php
/**
 * Базовый класс отношений
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Sfcms\Data;

use Sfcms\Data\Relation\Exception;
use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Db\Criteria;
use Sfcms\Model;

abstract class Relation
{
    protected $relation;
    protected $key;
    /** @var Model */
    protected $model;
    /** @var Object */
    protected $obj;

    public function __construct($field, Object $obj)
    {
        $this->obj      = $obj;
        $relation       = $obj->getModel()->relation();
        $this->relation = $relation[$field];
        $this->key      = $this->relation[2];
        $this->model    = $obj->getModel($this->relation[1]);
    }

    public abstract function find();

    public abstract function with(Collection $collection, $rel);

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->relation[1];
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->relation[2];
    }

    /**
     * Подготовит условие для поиска
     * @param $keys
     *
     * @return Criteria
     * @throws Exception
     */
    protected function prepareCond($keys)
    {
        $criteria = $this->model->createCriteria();
        if (!$keys) {
            // todo Могут быть проблемы производительности. Придумать лучее решение
            // todo Возникает ошибка, если вызвать связь на объект, у которого нет своего id (т.е. не сохраненный)
            throw new Exception('Keys not defined');
        }
        $cond   = array(is_array($keys) ? "`{$this->key}` IN (:key)" : "`{$this->key}` = :key");
        $params = array(":key" => $keys);
        if (isset($this->relation['where']) && is_array($this->relation['where'])) {
            foreach ($this->relation['where'] as $key => $value) {
                $cond[]   = "{$key} = ?";
                $params[] = $value;
            }
        }
        if (isset($this->relation['order'])) {
            $criteria->order = $this->relation['order'];
        }
        if (isset($this->relation['limit'])) {
            $criteria->limit = $this->relation['limit'];
        }
        $criteria->condition = implode(' AND ', $cond);
        $criteria->params    = $params;

        return $criteria;
    }

}

<?php
/**
 * Отношение "Содержит один"
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Sfcms\Data\Relation;

use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Data\Relation;

class One extends Relation
{
    public function with(Collection $collection, $rel)
    {
        $keys = array_map(
            function ($obj) {
                /** @var $obj Object */
                return $obj->getId();
            },
            iterator_to_array($collection)
        );
        if (count($keys)) {
            try {
                $cond = $this->prepareCond($keys);
            } catch (Exception $e) {
                return;
            }
            if (isset($this->relation['with'])) {
                $this->model->with($this->relation['with']);
            }
            // Загружаем объекты в Object Watcher
            $objects = $this->model->findAll($cond);

            foreach ($objects as $obj) {
                $item = $collection->getById($obj->{$this->key});
                $item->$rel = $obj;
            }
        }
    }

    public function find()
    {
        try {
            $cond = $this->prepareCond( $this->obj->getId() );
        } catch ( Exception $e ) {
            return false;
        }
        if (isset($this->relation['with'])) {
            $this->model->with($this->relation['with']);
        }
        $objRel = $this->model->find( $cond );
        if ( $objRel ) {
            return $objRel;
        }
        return null;
    }
}

<?php
/**
 * Вернет кол-во объектов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Sfcms\Data\Relation;

use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Data\Relation;

class Stat extends Relation
{
    public function with( Collection $collection , $rel)
    {
    }

    public function find()
    {
        return $this->model->count( " {$this->key} IN (:key) ", array( ":key"=> $this->obj->getId() ) );
    }
}

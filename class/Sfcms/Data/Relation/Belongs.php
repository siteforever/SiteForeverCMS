<?php
/**
 * Отношение "Принадлежит"
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Sfcms\Data\Relation;

use Sfcms\Data\Collection;
use Sfcms\Data\Relation;

class Belongs extends Relation
{
    public function with( Collection $collection )
    {
        $keys = array();
        foreach ( $collection as $obj ) {
            if( $obj->{$this->key} ) {
                $keys[ ] = $obj->{$this->key};
            }
        }
        $keys = array_unique( $keys, SORT_NUMERIC );
        if ( count( $keys ) > 0 ) {
            $criteria = $this->model->createCriteria();
            $criteria->condition = 'id IN (:id)';
            $criteria->params    = array(':id'=>$keys);
            if (isset($this->relation['order'])) {
                $criteria->order = $this->relation['order'];
            }
            if (isset($this->relation['with'])) {
                $this->model->with($this->relation['with']);
            }
            $objects = $this->model->findAll( $criteria );
            /** @var $o Object */
            foreach ( $objects as $o );
        }
    }

    public function find()
    {
        if( $this->obj->{$this->key} ) {
            if (isset($this->relation['with'])) {
                $this->model->with($this->relation['with']);
            }
            return $this->model->find( $this->obj->{$this->key} );
        }
        return null;
    }
}

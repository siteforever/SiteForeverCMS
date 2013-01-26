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
    public function with( Collection $collection )
    {
        $keys = array();
        /** @var $obj Object */
        foreach ( $collection as $obj ) {
            $keys[] = $obj->getId();
        }
        if ( count( $keys ) ) {
            try {
                $cond = $this->prepareCond( $keys );
            } catch ( Exception $e ) {
                return;
            }
            $objects = $this->model->findAll( $cond );
            /** @var $o Object */
            foreach ( $objects as $o );
        }
    }

    public function find()
    {
        try {
            $cond = $this->prepareCond( $this->obj->getId() );
        } catch ( Exception $e ) {
            return false;
        }
        $objRel = $this->model->find( $cond );
        if ( $objRel ) {
            return $objRel;
        }
        return null;
    }
}

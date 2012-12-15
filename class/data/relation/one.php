<?php
/**
 * Отношение "Содержит один"
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Data_Relation_One extends Data_Relation
{
    public function with( Data_Collection $collection )
    {
        $keys = array();
        foreach ( $collection as $obj ) {
            $keys[] = $obj->getId();
        }
        if ( count( $keys ) ) {
            try {
                $cond = $this->prepareCond( $keys );
            } catch ( Data_Relation_Exception $e ) {
                return;
            }
            $objects = $this->model->findAll( $cond );
            /** @var $o Data_Object */
            foreach ( $objects as $o );
        }
    }

    public function find()
    {
        try {
            $cond = $this->prepareCond( $this->obj->getId() );
        } catch ( Data_Relation_Exception $e ) {
            return;
        }
        $objRel = $this->model->find( $cond );
        if ( $objRel ) {
            return $objRel;
        }
        return null;
    }
}

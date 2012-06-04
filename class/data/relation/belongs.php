<?php
/**
 * Отношение "Принадлежит"
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
class Data_Relation_Belongs extends Data_Relation
{
    public function with( Data_Collection $collection )
    {
        $keys = array();
        foreach ( $collection as $obj ) {
            if( $obj->{$this->key} ) {
                $keys[ ] = $obj->{$this->key};
            }
        }
        $keys = array_unique( $keys, SORT_NUMERIC );
        if ( count( $keys ) > 0 ) {
            $objects = $this->model->findAll( "id IN ( " . implode( ",", $keys ) . " )" );
            /** @var $o Data_Object */
            foreach ( $objects as $o );
        }
    }

    public function find()
    {
        if( $this->obj->{$this->key} ) {
            return $this->model->find( $this->obj->{$this->key} );
        }
        return null;
    }
}

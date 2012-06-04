<?php
/**
 * Отношение "Принадлежит *"
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Data_Relation_Many extends Data_Relation
{
    public function with( Data_Collection $collection )
    {
        $keys = array();
        foreach ( $collection as $obj ) {
            $keys[] = $obj->getId();
        };
        if ( count( $keys ) ) {
            $objects = $this->model->findAll( " {$this->key} IN ( " . implode( ",", $keys ) . " ) " );
            foreach ( $objects as $o );
        }
    }

    public function find()
    {
        $criteria = array(
            'cond'  => " {$this->key} IN (:key) ",
            'params'=> array( ":key"=> $this->obj->getId() ),
        );
        return $this->model->findAll( $criteria );
    }
}

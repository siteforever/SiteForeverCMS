<?php
/**
 * Отношение "Принадлежит 1"
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
            $objects = $this->model->findAll( " $this->key IN ( " . implode( ",", $keys ) . " ) " );
            /** @var $o Data_Object */
            foreach ( $objects as $o );
        }
    }

    public function find()
    {
        $objRel = $this->model->find( array(
            'crit' => " {$this->key} = ? ",
            'params' => array( $this->obj->getId() ),
        ) );
        if ( $objRel ) {
            return $objRel;
        }
        return null;
    }

}

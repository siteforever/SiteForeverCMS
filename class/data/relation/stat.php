<?php
/**
 * Вернет кол-во объектов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Data_Relation_Stat extends Data_Relation
{
    public function with( Data_Collection $collection )
    {
    }

    public function find()
    {
        return $this->model->count( " {$this->key} IN (:key) ", array( ":key"=> $this->obj->getId() ) );
    }
}

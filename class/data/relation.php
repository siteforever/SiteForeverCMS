<?php
/**
 * Базовый класс отношений
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
class Data_Relation_Exception extends Exception {}

abstract class Data_Relation
{
    protected $relation;
    protected $key;
    /** @var Sfcms_Model */
    protected $model;
    /** @var Data_Object */
    protected $obj;

    public function __construct( $field, Data_Object $obj )
    {
        $this->obj      = $obj;
        $relation       = $obj->getModel()->relation();
        $this->relation = $relation[ $field ];
        $this->key      = $this->relation[ 2 ];
        $this->model    = $obj->getModel( $this->relation[ 1 ] );
    }

    public abstract function find();

    public abstract function with( Data_Collection $collection );

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->relation[ 1 ];
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->relation[ 2 ];
    }

}

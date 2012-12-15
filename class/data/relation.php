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

    /**
     * Подготовит условие для поиска
     * @param $keys
     *
     * @return Db_Criteria
     */
    protected function prepareCond( $keys )
    {
        $criteria = $this->model->createCriteria();
        if ( ! $keys ) {
            /* todo Могут быть проблемы производительности. Придумать лучее решение
               Возникает ошибка, если вызвать связь на объект, у которого нет своего id (т.е. не сохраненный)
            */
            throw new Data_Relation_Exception('Keys not defined');
        }
        $cond = array( is_array($keys) ? "{$this->key} IN (:key)" : "{$this->key} = :key");
        $params = array( ":key"=> $keys );
        if ( isset( $this->relation['where'] ) && is_array( $this->relation['where'] ) ) {
            foreach( $this->relation['where'] as $key => $value ) {
                $cond[] = "{$key} = ?";
                $params[] = $value;
            }
        }
        if ( isset( $this->relation['order'] ) ) {
            $criteria->order = $this->relation['order'];
        }
        if ( isset( $this->relation['limit'] ) ) {
            $criteria->limit = $this->relation['limit'];
        }
        $criteria->condition = implode(' AND ', $cond);
        $criteria->params = $params;
        return $criteria;
    }

}

<?php
/**
 * Отношение "Содержит несколько"
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Data_Relation_Many extends Data_Relation
{
    static protected $_cache = array();

    public function with( Data_Collection $collection )
    {
        $keys = array();
        /** @var $obj Data_Object */
        foreach ( $collection as $obj ) {
            $keys[] = $obj->getId();
        };
        if ( count( $keys ) ) {
            try {
                $cond = $this->prepareCond( $keys );
            } catch ( Data_Relation_Exception $e ) {
                return;
            }
            $objects = $this->model->findAll( $cond );
            foreach ( $objects as $obj ) {
                $this->addCache( $obj->{$this->key}, $obj );
            };
        }
    }

    /**
     * @return Data_Collection
     */
    public function find()
    {
        if ( null === $this->getCache( $this->obj->getId() ) ) {
            $with = array();
            if ( isset( $this->relation['with'] ) ) {
                $with = $this->relation['with'];
            }
            try {
                $cond = $this->prepareCond( $this->obj->getId() );
            } catch( Data_Relation_Exception $e ) {
                return null;
            }
            $this->setCache( $this->obj->getId(), $this->model->with( $with )->findAll( $cond ) );
        }
        return $this->getCache( $this->obj->getId() );
    }

    /**
     * @param $id
     * @param $collection
     */
    protected function setCache( $id, Data_Collection $collection )
    {
        self::$_cache[ $this->getModelName() ][ $this->key ][ $id ] = $collection;
    }

    /**
     * @param $id
     * @param $obj
     */
    protected function addCache( $id, Data_Object $obj )
    {
        if ( empty( self::$_cache[ $this->getModelName() ][ $this->key ][ $id ] ) ) {
            self::$_cache[ $this->getModelName() ][ $this->key ][ $id ] = new Data_Collection();
        }
        self::$_cache[ $this->getModelName() ][ $this->key ][ $id ]->add( $obj );
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function getCache( $id )
    {
        if ( isset( self::$_cache[ $this->getModelName() ][ $this->key ][ $id ] ) ) {
            return self::$_cache[ $this->getModelName() ][ $this->key ][ $id ];
        }
        return null;
    }
}

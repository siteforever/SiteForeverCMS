<?php
/**
 * Коллекция фильтров
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_Filter_Collection
{
    /**
     * @var array
     */
    protected $_filters = array();

    /**
     * @var array
     */
    protected $_parents = array();

    /**
     * @var boolean
     */
    protected $_prepared = false;

    /**
     * @param int $id
     * @return Sfcms_Filter
     */
    public function setFilter( $id )
    {
        $this->_prepared = false;
        $filter = new Sfcms_Filter( $id );
        $filter->setCollection( $this );
        $this->_filters[ $id ]  = $filter;
        return $filter;
    }

    /**
     * @param int $id
     * @return Sfcms_Filter
     */
    public function getFilter( $id )
    {
        if ( isset( $this->_filters[ $id ] )) {
            return $this->_filters[ $id ];
        }
        return null;
    }

    /**
     * @param int $id
     */
    public function clearFilter( $id = null )
    {
        $this->_prepared = false;
        if ( null === $id ) {
            $this->_filters = array();
        } elseif ( isset( $this->_filters[ $id ] )) {
            unset( $this->_filters[ $id ] );
        }
    }

    /**
     * Подготовка фильтра
     * @return void
     */
    public function prepare()
    {
        /**
         * @var Sfcms_Filter $filter
         */
        foreach ( $this->_filters as $filter ) {
            $parents = $filter->getParent();
            foreach ( $parents as $parent ) {
                $this->_parents[ $parent ][ $filter->getId() ] = $filter;
            }
        }
        $this->_prepared = true;
    }

    /**
     * @param $id
     * @return array
     * @throws RuntimeException
     */
    public function getParents( $id )
    {
        if ( ! $this->_prepared ) {
            throw new RuntimeException('Need prepared data');
        }
        if ( isset( $this->_parents[ $id ] ) ) {
            return $this->_parents[ $id ];
        }
        return array();
    }
}

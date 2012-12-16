<?php
/**
 * Фильтер для товаров в каталоге
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_Filter implements Iterator
{
    /**
     * @var Sfcms_Filter_Collection
     */
    protected $_collection = null;
    /**
     * @var int
     */
    protected $_id = null;
    /**
     * @var array
     */
    protected $_parent = array();
    /**
     * @var array
     */
    protected $_groups  = array();

    /**
     * @var boolean
     */
    protected $_prepared = false;

    /**
     * Указатель на тек. позицию.
     * @var int
     */
    protected $_pointer = 0;

    /**
     * @param $id
     */
    public function __construct( $id )
    {
        $this->_id = $id;
    }

    /**
     * @param Sfcms_Filter_Collection $collection
     */
    public function setCollection( Sfcms_Filter_Collection $collection )
    {
        $this->_prepared = false;
        $this->_collection = $collection;
    }

    /**
     * @return Sfcms_Filter_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Установить группу фильтра
     * @param $n     Номер фильтра
     * @param $name  Наименования фильтра
     * @param $data  Данные для фильтра
     * @return Sfcms_Filter
     */
    public function setFilterGroup( $n, $name, $data )
    {
        $this->_prepared = false;
        $filter = null;
        switch ( $data ) {
            case 'range':
                $filter = new Sfcms_Filter_Group_Range( $n, $name, $data );
                break;
            case 'categories':
                $filter = new Sfcms_Filter_Group_Categories( $n, $name, $data );
                break;
            default:
                $filter = new Sfcms_Filter_Group_Array( $n, $name, $data );
        }
        $this->_groups[ $n ] = $filter;
        return $this;
    }

    /**
     * @param $n
     * @return Sfcms_Filter_Group
     */
    public function getFilterGroup( $n )
    {
        if( isset( $this->_groups[ $n ] ) ) {
            return $this->_groups[ $n ];
        }
        return null;
    }

    /**
     * @param null $n
     */
    public function clearFilterGroup( $n = null )
    {
        $this->_prepared = false;
        if ( null === $n ) {
            $this->_groups  = array();
        } elseif ( isset( $this->_groups[ $n ] ) ) {
            unset( $this->_groups[ $n ] );
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param int|array $parent
     * @return Sfcms_Filter
     */
    public function setParent( $parent )
    {
        $this->_prepared = false;
        if ( is_array( $parent ) ) {
            $this->_parent = $parent;
        } else {
            $this->_parent = array( $parent );
        }
        foreach( $this->_parent as $id ) {
            $filter = $this->_collection->getFilter( $id );
            $this->_parent = array_merge( $this->_parent, $filter->getParent() );
        }
        return $this;
    }

    /**
     * @param int|array $parent
     * @return Sfcms_Filter
     */
    public function addParent( $parent )
    {
        $this->_prepared = false;
        if ( is_array( $parent ) ) {
            $this->_parent = array_merge( $this->_parent, $parent );
        } else {
            $this->_parent[] = $parent;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Заполнит фильтры типа categories
     * Выберет дочерние значения фильтров, если есть
     * @param Model_Catalog $model
     */
    public function prepare( Model_Catalog $model )
    {
        $this->fillData( $this->_id, $model );
        $this->fillChildren();
    }


    /**
     * Заполнить данные
     * @param int $id
     * @param Sfcms_Model $model
     */
    public function fillData( $id, Sfcms_Model $model )
    {
        /** @var Sfcms_Filter_Group $fGroup */
        foreach ( $this->_groups as $fGroup ) {
            $fGroup->fillData( $id, $model );
        }
    }

    /**
     * Заполнить поля дочерними значениями
     */
    protected function fillChildren()
    {
        /**
         * @var Sfcms_Filter $filter
         * @var Sfcms_Filter_Group $group
         */
        if ( $this->_collection && $this->_collection->getParents( $this->_id ) ) {
            $values = array();
            foreach ( $this->_collection->getParents( $this->_id ) as $filter ) {
                $group  = $filter->getFilterGroup(0);
                if ( ! $group ) {
                    continue;
                }
                $data   = $group->getData();
                if ( ! $data ) {
                    continue;
                }
                foreach ( $data as $d ) {
                    $values[] = $d;
                }
            }
            $values = array_unique( $values );
            $group  = $this->getFilterGroup(0);
            if ( ! $group && count( $values ) ) {
                $this->setFilterGroup( 0, '', $values );
            } elseif ( $group ) {
                $group->setData( $values );
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->getFilterGroup( $this->_pointer );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $result = $this->getFilterGroup( $this->_pointer );
        if ( $result ) {
            $this->_pointer++;
        }
        while ( $this->_pointer < 10 && null == $this->getFilterGroup( $this->_pointer ) ) {
            $this->_pointer++;
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, integer
     * 0 on failure.
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return ( ! is_null( $this->current() ) );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->_pointer = 0;
    }
}
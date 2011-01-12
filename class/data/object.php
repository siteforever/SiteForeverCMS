<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Object implements ArrayAccess, Iterator
{

    private $data   = array();

    /**
     * @var Model
     */
    private $model  = null;

    /**
     * @var Data_Table
     */
    private $table  = null;

    /**
     * Поля таблицы
     * @var array
     */
    private $field_names   = array();

    /**
     * @param Model $model
     * @param array $data
     */
    public function __construct( $model, $data )
    {
        $this->model    = $model;
        $this->table    = $model->getTable();
        
        foreach ( $this->table->getFields() as $field ) {
            $this->field_names[ $field->getName() ]    = $field;
        }

        $this->setAttributes( $data );
    }

    function __get($name)
    {
        if ( $this->offsetExists( $name ) ) {
            return $this->offsetGet( $name );
        }
    }

    function __set($name, $value)
    {
        $this->offsetSet( $name, $value );
    }

    function __isset($name)
    {
        return $this->offsetExists( $name );
    }


    /**
     * Вернет список установленных атрибутов
     * @return array
     */
    public function getAttributes()
    {
        return $this->data;
    }

    /**
     * Установить список атрибутов
     * @param array $data
     * @return void
     */
    public function setAttributes( $data = array() )
    {
        foreach( $data as $k => $d ) {
            $this->offsetSet( $k, $d );
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean Returns true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset( $this->data[ $offset ] );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        if ( $this->offsetExists( $offset ) ) {
            return $this->data[ $offset ];
        }
        return null;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ( isset( $this->field_names[ $offset ] ) )
            if ( $this->field_names[ $offset ]->validate( $value ) )
                $this->data[ $offset ]  = $value;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset( $this->data[ $offset ] );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current( $this->data );
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
        return key( $this->data );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        return next( $this->data );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        return reset( $this->data );
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
        return $this->current() ? true : false;
    }
}

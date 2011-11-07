<?php
/**
 * Интервейс контейнера для данных
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

abstract class Data_Object implements ArrayAccess//, Iterator
{
    protected $data   = array();

    /**
     * @var Model
     */
    protected $model  = null;

    /**
     * @var Data_Table
     */
    protected $table  = null;

    /**
     * Поля таблицы
     * @var array
     */
    protected $field_names   = array();

    /**
     * @param Model $model
     * @param array $data
     */
    public function __construct( Model $model, $data )
    {
        $this->model    = $model;
        $this->table    = $model->getTable();

//        foreach ( $this->table->getFields() as $field ) {
//            $this->field_names[ $field->getName() ]    = $field;
//        }

        $this->setAttributes( $data );

        // @TODO Пока не будем помечать новые объекты для добавления
        /*if ( is_null( $this->getId() ) ) {
            $this->markNew();
        }*/
    }

    /**
     * @param $name
     * @return array|Data_Object|mixed|null
     */
    function __get($name)
    {
        return $this->get( $name );
    }

    /**
     * @param $key
     * @return array|Data_Object|mixed|null
     */
    function get( $key )
    {
        $relation = $this->model->relation();
        if ( isset( $relation[ $key ] ) ) {
            return $this->model->findByRelation( $key, $this );
        }
        if ( $this->offsetExists( $key ) ) {
            return $this->offsetGet( $key );
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    function __set($name, $value)
    {
        $this->set( $name, $value );
    }

    /**
     * @param $key
     * @param $value
     * @return Data_Object
     */
    function set( $key, $value )
    {
        if ( isset( $this->field_names[ $key ] ) ) {
            if ( $this->field_names[ $key ]->validate( $value ) !== false ) {
                if ( ( $this->offsetExists( $key ) && $this->data[ $key ] !== $value ) ||
                     ! $this->offsetExists( $key )
                ) {
                    $this->data[ $key ]  = $value;
                    $this->markDirty();
                }
            }
        }
        else {
            $this->data[$key]    = $value;
        }
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    function __isset($name)
    {
        return $this->offsetExists( $name );
    }

    /**
     * @param $name
     * @return void
     */
    function __unset($name)
    {
        unset( $this->data[$name] );
        $this->markDirty();
    }

    /**
     * @return void
     */
    function __clone()
    {
        unset( $this->data['id'] );
        $this->markNew();
    }

    /**
     * @return string
     */
    function __toString()
    {
        return get_class( $this );
    }

    /**
     * Установить id
     * @param  $id
     * @return void
     */
    function setId( $id )
    {
        if ( ! isset( $this->data['id'] ) && is_numeric( $id ) ) {
            $this->data['id']   = $id;
            return;
        }
        throw new Exception('Attempting to set an existing object id');
    }

    /**
     * Вернет id
     * @return int|null
     */
    function getId()
    {
        if ( isset( $this->data['id'] ) && $this->data['id'] ) {
            return $this->data['id'];
        }
        return null;
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
//            $this->data[ $k ] = $d;
            $this->set( $k, $d );
        }
    }

    /**
     * Вернет модель данных
     * @param string $model
     * @return Model
     */
    public function getModel( $model = '' )
    {
        if ( '' === $model )
            return $this->model;
        else
            return Model::getModel($model);
    }

    public function getTable()
    {
        return $this->model->getTable();
    }

    /**
     * Сохранение
     * @return int
     */
    public function save()
    {
        return $this->model->save( $this );
    }

    /**
     * Как новый
     * @return void
     */
    function markNew()
    {
        Data_Watcher::addNew( $this );
    }

    /**
     * Как удаленный
     * @return void
     */
    function markDeleted()
    {
        Data_Watcher::addDelete( $this );
    }

    /**
     * На обновление
     * @return void
     */
    function markDirty()
    {
        Data_Watcher::addDirty( $this );
    }

    /**
     * Стереть везде
     * @return void
     */
    function markClean()
    {
        Data_Watcher::addClean( $this );
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
        $this->set( $offset, $value );
        return $this;
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
        if ( isset ( $this->data[ $offset ] ) ) {
            unset( $this->data[ $offset ] );
            $this->markDirty();
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
        return current( $this->data );
    }

}

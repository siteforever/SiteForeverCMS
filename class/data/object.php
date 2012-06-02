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
     * @var Sfcms_Model
     */
    protected $model  = null;

    /**
     * @var Data_Table
     */
    protected $table  = null;

    /**
     * @param Sfcms_Model $model
     * @param array $data
     */
    public function __construct( Sfcms_Model $model, $data )
    {
        $this->model    = $model;
        $this->table    = $model->getTable();

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
    public function __get($name)
    {
        return $this->get( $name );
    }

    /**
     * @param $key
     * @return array|Data_Object|mixed|null
     */
    public function get( $key )
    {
        $relation = $this->model->relation();
        if ( isset( $relation[ $key ] ) ) {
            return $this->model->findByRelation( $key, $this );
        } else if ( method_exists( $this, 'get'.$key ) && 'id' != $key ) {
            return $this->{'get'.$key}();
        } else if ( isset( $this->data[ $key ] ) ) {
            return $this->data[ $key ];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set( $name, $value );
    }

    /**
     * @param $key
     * @param $value
     * @return Data_Object
     */
    public function set( $key, $value )
    {
        if ( method_exists( $this, 'set'.$key ) && 'id' != $key ) {
            $method = 'set'.$key;
            $this->$method( $value );
        } else {
            if ( ! isset( $this->data[$key] ) || ( isset( $this->data[$key] ) && $this->data[$key] != $value ) ) {
                $this->markDirty();
            }
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists( $name );
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        $this->data[$name] = null;
        $this->markDirty();
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->data['id'] = null;
        $this->markNew();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class( $this );
    }

    /**
     * Установить id
     * @param $id
     *
     * @return mixed
     * @throws Exception
     */
    public function setId( $id )
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
    public function getId()
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
            $this->set( $k, $d );
        }
    }

    /**
     * Вернет модель данных
     * @param string $model
     * @return Sfcms_Model
     */
    public function getModel( $model = '' )
    {
        if ( '' === $model )
            return $this->model;
        else
            return Sfcms_Model::getModel($model);
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
    public function markNew()
    {
        Data_Watcher::addNew( $this );
    }

    /**
     * Как удаленный
     * @return void
     */
    public function markDeleted()
    {
        Data_Watcher::addDelete( $this );
    }

    /**
     * На обновление
     * @return void
     */
    public function markDirty()
    {
        Data_Watcher::addDirty( $this );
    }

    /**
     * Стереть везде
     * @return void
     */
    public function markClean()
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
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * </p>
     */
    public function offsetExists($offset)
    {
        return (bool) null !== $this->get( $offset );
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
        return $this->get( $offset );
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
     * @return Data_Object
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
            $this->data[ $offset ] = null;
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

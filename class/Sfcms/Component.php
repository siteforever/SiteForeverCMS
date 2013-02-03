<?php
/**
 * Component SiteForeverCMS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

use App;
use Sfcms\Data\Object;

/**
 * @property $attributes
 */
abstract class Component implements \ArrayAccess//, Iterator;
{
    protected $data   = array();

    /**
     * @return App
     */
    public function app()
    {
        return App::getInstance();
    }

    /**
     * Логирует сообщение
     * @param        $message
     * @param string $label
     */
    public function log( $message, $label = '' )
    {
        $this->app()->getLogger()->log( $message, $label );
    }


    /**
     * @param $key
     * @return mixed
     */
    public function get( $key )
    {
        $method = 'get'.ucfirst(strtolower($key));
        if ( method_exists( $this, $method ) && 'id' != $key ) {
            return $this->$method();
        } else if ( isset( $this->data[ $key ] ) ) {
            return $this->data[ $key ];
        }
        $method = 'onGet'.$key;
        if ( method_exists( $this, $method ) ) {
            $this->$method();
        }
        return null;
    }

    /**
     * @param $key
     * @param $value
     * @return self
     */
    public function set( $key, $value )
    {
        $method = 'set'.$key;
        if ( method_exists( $this, $method ) && 'id' != $key ) {
            $this->$method( $value );
        } else {
            $this->data[$key] = $value;
        }

        $method = 'onSet'.ucfirst($key);
        if ( method_exists( $this, $method ) ) {
            $this->$method();
        }

        return $this;
    }


    /**
     * Вернет список установленных атрибутов
     * @return array
     */
    public function getAttributes()
    {
        $result = array();
        foreach ( $this->data as $key => $val ) {
            $result[ $key ] = $this->get( $key );
        }
        return $result;
    }

    /**
     * Установить список атрибутов
     * @param array $data
     * @return self
     */
    public function setAttributes( $data = array() )
    {
        foreach( $data as $k => $d ) {
            $this->set( $k, $d );
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class( $this );
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        unset( $this->data[$name] );
    }

    /**
     * @param $name
     * @return array|Object|mixed|null
     */
    public function __get($name)
    {
        return $this->get( $name );
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
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists( $name );
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
     * @return Object
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
            $this->markDirty(); // TODO WTF!!!
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
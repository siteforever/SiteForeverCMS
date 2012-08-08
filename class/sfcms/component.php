<?php
/**
 * Component SiteForeverCMS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

abstract class Component implements \ArrayAccess//, Iterator;
{
    protected $data   = array();

    /**
     * @return \App
     */
    public function app()
    {
        return \App::getInstance();
    }

    /**
     * Логирует сообщение
     * @param        $message
     * @param string $label
     */
    protected function log( $message, $label = '' )
    {
        $this->app()->getLogger()->log( $message, $label );
    }


    /**
     * @param $key
     * @return mixed
     */
    public function get( $key )
    {
        if ( method_exists( $this, 'get'.$key ) && 'id' != $key ) {
            return $this->{'get'.$key}();
        } else if ( isset( $this->data[ $key ] ) ) {
            return $this->data[ $key ];
        }
        return null;
    }

    /**
     * @param $key
     * @param $value
     * @return Component
     */
    public function set( $key, $value )
    {
        if ( method_exists( $this, 'set'.$key ) && 'id' != $key ) {
            $method = 'set'.$key;
            $this->$method( $value );
        } else {
            $this->data[$key] = $value;
        }
        return $this;
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
        $this->data[$name] = null;
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

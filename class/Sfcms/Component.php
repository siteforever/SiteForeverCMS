<?php
/**
 * Component SiteForeverCMS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

use App;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\EventDispatcher\Event;

/**
 * @property $attributes
 */
abstract class Component implements \ArrayAccess//, Iterator;
{
    protected $data   = array();

    /**
     * @return App
     */
    public static function app()
    {
        return App::cms();
    }

    /**
     * @return i18n
     */
    public function i18n()
    {
        return $this->app()->getContainer()->get('i18n');
    }

    /**
     * Напечатать переведенный текст
     * @param string $cat
     * @param string $text
     * @param array $params
     * @return mixed
     */
    public function t($cat, $text = '', $params = array())
    {
        return call_user_func_array(array($this->i18n(),'write'), func_get_args());
    }

    /**
     * Логирует сообщение
     * @param        $message
     * @param string $label
     */
    public function log($message, $label = '')
    {
        $this->app()->getLogger()->log($message, $label);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $result = null;
        $method = 'get' . ucfirst($key);
        if (is_callable(array($this, $method)) && 'getId' != $method) {
            $result = $this->$method();
        } else if (isset($this->data[$key])) {
            $result = $this->data[$key];
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @return self
     */
    public function set($key, $value)
    {
        $method = 'set' . ucfirst($key);
        if (is_callable(array($this, $method)) && 'setId' != $method) {
            $this->$method($value);
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
        $result = array();
        foreach ($this->data as $key => $val) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }

    /**
     * Установить список атрибутов
     * @param array $data
     * @return self
     */
    public function setAttributes($data = array())
    {
        foreach($data as $k => $d) {
            $this->set($k, $d);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * @param $name
     * @return array|Object|mixed|null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Dispatch named event
     *
     * @param string $eventName
     * @param Event $event
     *
     * @return Event
     */
    public function trigger($eventName, Event $event)
    {
        $this->log(sprintf('trigger: %s (%s)', $eventName, $event->getName()));
        return $this->app()->getEventDispatcher()->dispatch($eventName, $event);
    }

    /**
     * @param $eventName
     * @param $callback
     * @param $priority
     *
     * @throws RuntimeException
     */
    public function on($eventName, $callback, $priority = 0)
    {
        if (!(is_array($callback) || $callback instanceof \Closure)) {
            throw new RuntimeException('"$callback" must be Array or Closure');
        }
        $this->app()->getEventDispatcher()->addListener($eventName, $callback, $priority);
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
        $this->__unset($offset);
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

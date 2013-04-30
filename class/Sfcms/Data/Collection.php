<?php
namespace Sfcms\Data;

use Sfcms\Model;

/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Collection implements \Iterator
{
    //abstract class Mapper_Collection implements Iterator
    /**
     * Массив необработанных данных
     * @var array
     */
    protected $_raw = array();

    /**
     * Количество записей
     * @var int
     */
    protected $_total = 0;

    /**
     * Объект маппера
     * @var Model
     */
    protected $_mapper;

    /**
     * Индекс для поиска по id
     * @var array
     */
    protected $_index = null;

    /**
     * Указатель на тек. позицию.
     * @var int
     */
    private $_pointer = 0;

    /**
     * Список объектов
     * @var array
     */
    private $_objects = array();

    /**
     * Создаст коллекцию
     * @param       $raw
     * @param Model $mapper
     */
    public function __construct($raw = null, Model $mapper = null)
    {
        if (!is_null($raw) && $raw && !is_null($mapper)) {
            $this->_raw   = array_values($raw);
            $this->_total = count($raw);
        }
        $this->_mapper = $mapper;
    }

    /**
     * Добавить элемент в коллекцию
     * @param Object $obj
     *
     * @return $this
     */
    public function add(Object $obj)
    {
        if (in_array($obj, $this->_objects, true)) {
            return $this;
        }
        $this->notifyAccess();
        $this->_raw[$this->_total]     = $obj->attributes;
        $this->_objects[$this->_total] = $obj;
        $this->_total++;

        return $this;
    }

    /**
     * Вернет объект из коллекции по его id
     *
     * @param $id
     *
     * @return Object|null
     */
    public function getById($id)
    {
        if (null == $this->_index) {
            $this->_index = array();
            foreach ($this->_raw as $key => $val) {
                if (isset($val['id'])) {
                    $this->_index[$val['id']] = $key;
                }
            }
        }

        return isset($this->_index[$id]) ? $this->getRow($this->_index[$id]) : null;
    }

    /**
     * Удалит элемент из коллекции
     * @param boolean|int|Object $key
     */
    public function del($key = false)
    {
        if ($key === false) {
            $key = $this->_pointer;
        }
        if ($key instanceof Object) {
            foreach ($this->_raw as $k => $a) {
                if ($a['id'] == $key->getId()) {
                    $key = $k;
                    break;
                }
            }
        }

        if (isset($this->_objects[$key]) || isset($this->_raw[$key])) {
            $this->notifyAccess();
            unset($this->_objects[$key]);
            unset($this->_raw[$key]);
            $this->_objects = array_values($this->_objects);
            $this->_raw     = array_values($this->_raw);
            $this->_total--;
        }
    }

    /**
     * Вернет количество записей
     */
    public function count()
    {
        return $this->_total;
    }

    /**
     * Расчитает сумму по нужной колонке
     * @param string $key
     *
     * @return int
     * @deprecated need using sum()
     */
    public function summa($key)
    {
        return $this->sum($key);
    }

    /**
     * Расчитает сумму по нужной колонке
     * @param string $key
     *
     * @return int
     */
    public function sum($key)
    {
        $result = 0;
        foreach ($this as $obj) {
            $result += $obj->$key;
        }

        return $result;
    }

    /**
     * Вернет массив, в котором содержатся значение определенной колонки
     * По возможности, индексирует по id
     *
     * @param string $name
     *
     * @return array
     * @throws \RuntimeException
     */
    public function column($name)
    {
        $result = array();
        foreach ($this as $obj) {
            if (isset($obj->id)) {
                $result[$obj->id] = $obj->$name;
            } else {
                $result[] = $obj->$name;
            }
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function notifyAccess()
    {
    }

    /**
     * Вернуть объект
     * @param int $num
     *
     * @return Object
     */
    public function getRow($num)
    {
        $this->notifyAccess();

        if ($num >= $this->_total || $num < 0) {
            return null;
        }

        if (isset($this->_objects[$num])) {
            return $this->_objects[$num];
        }

        if (isset($this->_raw[$num])) {
            $obj = $this->_mapper->createObject($this->_raw[$num]);
            // Предполагается, что данные для объекта были плучены из findAll()
            if ($obj->isStateCreate()) {
                $obj->markClean();
            }
            $this->_objects[$num] = $obj;
        }

        return $this->_objects[$num];
    }


    /**
     * @return Object
     */
    public function rewind()
    {
        $this->_pointer = 0;

        return $this->current();
    }

    /**
     * @return Object
     */
    public function current()
    {
        return $this->getRow($this->_pointer);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * @return Object
     */
    public function next()
    {
        $row = $this->getRow($this->_pointer);
        if ($row) {
            $this->_pointer++;
        }

        return $row;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return (!is_null($this->current()));
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->_raw;
    }

    /**
     * @return array
     */
    public function getObjects()
    {
        return iterator_to_array($this);
    }

}

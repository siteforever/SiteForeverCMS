<?php
/**
 * 
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */
 
class Data_Collection implements Iterator
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
     * @var Sfcms_Model
     */
    protected $_mapper;

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
     * @param $raw
     * @param Sfcms_Model $mapper
     */
    function __construct( $raw = null, Sfcms_Model $mapper = null )
    {
        if ( ! is_null( $raw ) && $raw && ! is_null( $mapper ) ) {
            $this->_raw      = array_values( $raw );
            $this->_total    = count( $raw );
        }
        $this->_mapper   = $mapper;
    }

    /**
     * Добавить элемент в коллекцию
     * @param $obj
     */
    function add( Data_Object $obj )
    {
        if ( in_array( $obj, $this->_objects, true ) ) {
            return $this;
        }
//        $class  = $this->targetClass();
//        if ( ! ( $obj instanceof $class ) ) {
//            throw new Exception("Это коллеция {$class}");
//        }

        $this->notifyAccess();
        $this->_raw[$this->_total]    = $obj->getData();
        $this->_objects[$this->_total] = $obj;
        $this->_total ++;
        return $this;
    }

    /**
     * Удалит элемент из коллекции
     * @param boolean|int|Data_Object $key
     */
    function del( $key = false )
    {
        if ( $key === false ) {
            $key = $this->_pointer;
        }
        if ( $key instanceof Data_Object ) {
            foreach ( $this->_raw as $k => $a ) {
                if ( $a['id'] == $key->getId() ) {
                    $key    = $k;
                    break;
                }
            }
        }

        if ( isset( $this->_objects[ $key ] ) || isset( $this->_raw[ $key ] ) )
        {
            $this->notifyAccess();
            unset( $this->_objects[ $key ] );
            unset( $this->_raw[ $key ] );
            $this->_objects = array_values ( $this->_objects );
            $this->_raw     = array_values ( $this->_raw );
            $this->_total--;
        }
    }

    /**
     * Вернет количество записей
     */
    function count()
    {
        return $this->_total;
    }

    /**
     * Расчитает сумму по нужной колонке
     * @param string $key
     * @return int
     */
    function summa($key)
    {
        $summa = 0;
        if ( isset( $this->_raw[0][$key] ) ) {
            foreach ( $this->_raw as $raw ) {
                $summa += $raw[$key];
            }
        }
        return $summa;
    }

    /**
     * Вернет имя класса объекта
     * @return string
     */
//    function targetClass()
//    {
//        if ( preg_match('/Mapper_(.*?)_Collection/', get_class( $this ), $match ) ) {
//            return 'Domain_'.$match[1];
//        }
//        else {
//            throw new Mapper_Exeption('Невозможно определить Target Class');
//        }
//    }

    /**
     * @return void
     */
    protected function notifyAccess()
    {

    }

    /**
     * Вернуть объект
     * @param int $num
     * @return Data_Object
     */
    public function getRow( $num )
    {
        $this->notifyAccess();

        if ( $num >= $this->_total || $num < 0 ) {
            return null;
        }

        if ( isset( $this->_objects[$num] ) ) {
            return $this->_objects[$num];
        }

        if ( isset( $this->_raw[$num] ) ) {
            $this->_objects[$num] = $this->_mapper->createObject( $this->_raw[$num] );
        }
        return $this->_objects[$num];
    }


    /**
     * @return Data_Object
     */
    function rewind()
    {
        $this->_pointer = 0;
        return $this->current();
    }

    /**
     * @return Data_Object
     */
    function current()
    {
        return $this->getRow( $this->_pointer );
    }

    /**
     * @return int
     */
    function key()
    {
        return $this->_pointer;
    }

    /**
     * @return Data_Object
     */
    function next()
    {
        $row = $this->getRow( $this->_pointer );
        if ( $row ) {
            $this->_pointer++;
        }
        return $row;
    }

    /**
     * @return bool
     */
    function valid()
    {
        return ( ! is_null( $this->current() ) );
    }

    /**
     * @return array|null
     */
    function getData()
    {
        return $this->_raw;
    }

    /**
     * @return array
     */
    function getObjects()
    {
        return $this->_objects;
    }

}

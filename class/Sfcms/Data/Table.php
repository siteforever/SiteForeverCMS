<?php
/**
 * Описание структуры данных
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

/**
 * 1. Инкапсулировать структуру таблицы
 * 2. Создать валидаторы для каждого поля
 *
 *
 */
namespace Sfcms\Data;

use Sfcms\Component;
use Sfcms\Data\Field;

abstract class Table extends Component
{
    /**
     * Тип таблицы
     * @var string
     */
    //protected $engine   = 'MyISAM';
    protected $engine   = 'InnoDB';

    /**
     * Список полей
     * @var array
     */
    protected static $fields   = array();

    /**
     * Построение запроса для создания таблицы
     * @return string
     */
    public function getCreateTable()
    {
        $ret = sprintf("CREATE TABLE `%s` (\n\t", static::getTable());

        $params = array();

        /**
         * @var Field $field
         */
        foreach ( $this->getFields() as $field ) {
            $params[] = $field->toString();
        }

        if ( $this->getPk() ) {
            if ( is_array($this->getPk()) ) {
                $pk = '`'.join('`,`', $this->getPk()).'`';
            } else {
                $pk = "`".str_replace(',', '`,`', $this->getPk())."`";
            }
            $params[] = "PRIMARY KEY ({$pk})";
        }

        foreach ( $this->getKeys() as $key => $val ) {

            $found = false;

            if ( is_array( $val ) ) {
                foreach ( $val as $v ) {
                    $subfound   = false;
                    foreach ( $this->getFields() as $field ) {
                        if ( $field->getName() == $v ) {
                            $subfound   = true;
                            break;
                        }
                    }
                    $found  = $found || $subfound;
                }
                $val    = implode(',', $val);
            }
            else {
                foreach ( $this->getFields() as $field ) {
                    if ( $field->getName() == $val ) {
                        $found = true;
                        break;
                    }
                }
            }

            if ( ! $found ) {
                //die('Key column doesn`t exist in table');
                throw new Exception("Key column '{$val}' doesn`t exist in table");
            }

            $val    = str_replace(',', '`,`', $val);
            if ( is_numeric($key) ) {
                $key    = $val;
            }
            $params[] = "KEY `{$key}` (`{$val}`)";
        }

        $ret .= join(",\n\t", $params)."\n";

        $ret .= ") ENGINE={$this->engine} DEFAULT CHARSET=utf8";
        return $ret;
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public function __toString()
    {
        return ( defined('DBPREFIX') ? DBPREFIX : '' ) . $this->getTable();
    }

    /**
     * Вернет имя таблицы
     * @return string
     * @throws Exception
     */
    public static function getTable()
    {
        throw new Exception(sprintf('Need declare method in "%s::%s()"', get_called_class(), __FUNCTION__));
    }


    /**
     * Создаст список полей
     * @return array
     * @throws Exception
     */
    protected static function doGetFields()
    {
        throw new Exception(sprintf('Need declare method in "%s::%s()"', get_called_class(), __FUNCTION__));
    }

    /**
     * Вернет список полей
     * @abstract
     * @return array
     */
    public static function getFields()
    {
        $class = get_called_class();
        if ( ! isset( static::$fields[ $class ] ) ) {
            static::$fields[ $class ] = static::doGetFields();
        }
        return static::$fields[ $class ];
    }

    /**
     * Вернет первичный ключ
     * @return string
     */
    protected function getPk()
    {
        return 'id';
    }

    /**
     * Вернет список индексных ключей
     * @return array
     */
    protected function getKeys()
    {
        return array();
    }
//
//    /**
//     * Поле целого чисиа
//     * @param  $name
//     * @param int $length
//     * @param bool $notnull
//     * @param null $default
//     * @param bool $autoincrement
//     * @return Field\Int
//     */
//    public function getInt( $name, $length = 11, $notnull = false, $default = null, $autoincrement = false )
//    {
//        return new Field\Int( $name, $length, $notnull, $default, $autoincrement );
//    }
//
//    /**
//     * Поле короткого целого
//     * @param  $name
//     * @param int $length
//     * @param bool $notnull
//     * @param null $default
//     * @param bool $autoincrement
//     * @return Field\Tinyint
//     */
//    public function getTinyint( $name, $length = 4, $notnull = false, $default = null, $autoincrement = false )
//    {
//        return new Field\Tinyint( $name, $length, $notnull, $default, $autoincrement );
//    }
//
//    /**
//     * Текстовое поле
//     * @param  $name
//     * @param bool $notnull
//     * @return Field\Text
//     */
//    public function getText( $name, $notnull = false )
//    {
//        $length     = null;
//        $default    = null;
//        $autoincrement  = null;
//        return new Field\Text( $name, $length, $notnull, $default, $autoincrement );
//    }
//
//    /**
//     * Строка переменной длины
//     * @param  $name
//     * @param int $length
//     * @param bool $notnull
//     * @param null $default
//     * @param bool $autoincrement
//     * @return Field\Varchar
//     */
//    public function getVarchar( $name, $length = 255, $notnull = false, $default = null, $autoincrement = false )
//    {
//        return new Field\Varchar( $name, $length, $notnull, $default, $autoincrement );
//    }
//
//    /**
//     * Число с плавающей точкой
//     * @param  $name
//     * @param string $length
//     * @param bool $notnull
//     * @param null $default
//     * @param bool $autoincrement
//     * @return Field\Decimal
//     */
//    public function getDecimal( $name, $length = '13,2', $notnull = false, $default = null, $autoincrement = false )
//    {
//        return new Field\Decimal( $name, $length, $notnull, $default, $autoincrement );
//    }

}

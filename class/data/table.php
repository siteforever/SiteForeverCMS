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

abstract class Data_Table
{
    /**
     * Тип базы
     * @var string
     */
    //protected $engine   = 'MyISAM';
    protected $engine   = 'InnoDB';

    /**
     * Список полей
     * @var array
     */
    protected $fields   = null;

    /**
     * Построение запроса для создания таблицы
     * @return string
     */
    public function getCreateTable()
    {
        $ret = "CREATE TABLE `{$this->getTable()}` (\n\t";

        $params = array();

        /**
         * @var Data_Field $field
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
                throw new Data_Exception("Key column '{$val}' doesn`t exist in table");
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
        return DBPREFIX.$this->getTable();
    }

    /**
     * Вернет имя таблицы
     * @abstract
     * @return string
     */
    abstract protected function getTable();



    /**
     * Создаст список полей
     * @abstract
     * @return array
     */
    abstract protected function doGetFields();

    /**
     * Вернет список полей
     * @abstract
     * @return array
     */
    public function getFields()
    {
        if ( is_null( $this->fields ) ) {
            $this->fields   = $this->doGetFields();
        }
        return $this->fields;
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

    /**
     * Поле целого чисиа
     * @param  $name
     * @param int $length
     * @param bool $notnull
     * @param null $default
     * @param bool $autoincrement
     * @return Data_Field_Int
     */
    public function getInt( $name, $length = 11, $notnull = false, $default = null, $autoincrement = false )
    {
        return new Data_Field_Int( $name, $length, $notnull, $default, $autoincrement );
    }

    /**
     * Поле короткого целого
     * @param  $name
     * @param int $length
     * @param bool $notnull
     * @param null $default
     * @param bool $autoincrement
     * @return Data_Field_Tinyint
     */
    public function getTinyint( $name, $length = 4, $notnull = false, $default = null, $autoincrement = false )
    {
        return new Data_Field_Tinyint( $name, $length, $notnull, $default, $autoincrement );
    }

    /**
     * Текстовое поле
     * @param  $name
     * @param bool $notnull
     * @return Data_Field_Text
     */
    public function getText( $name, $notnull = false )
    {
        $length     = null;
        $default    = null;
        $autoincrement  = null;
        return new Data_Field_Text( $name, $length, $notnull, $default, $autoincrement );
    }

    /**
     * Строка переменной длины
     * @param  $name
     * @param int $length
     * @param bool $notnull
     * @param null $default
     * @param bool $autoincrement
     * @return Data_Field_Varchar
     */
    public function getVarchar( $name, $length = 255, $notnull = false, $default = null, $autoincrement = false )
    {
        return new Data_Field_Varchar( $name, $length, $notnull, $default, $autoincrement );
    }

    /**
     * Число с плавающей точкой
     * @param  $name
     * @param string $length
     * @param bool $notnull
     * @param null $default
     * @param bool $autoincrement
     * @return Data_Field_Decimal
     */
    public function getDecimal( $name, $length = '13,2', $notnull = false, $default = null, $autoincrement = false )
    {
        return new Data_Field_Decimal( $name, $length, $notnull, $default, $autoincrement );
    }

}

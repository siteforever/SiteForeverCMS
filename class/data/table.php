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
     * @var string
     */
    protected $engine   = 'MyISAM';

    /**
     * Построение запроса для создания таблицы
     * @return string
     */
    function getCreateTable()
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
            }
            else {
                $pk = "`".str_replace(',', '`,`', $this->getPk())."`";
            }
            $params[] = "PRIMARY KEY ({$pk})";
        }

        foreach ( $this->getKeys() as $key => $val ) {
            $val    = str_replace(',', '`,`', $val);
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
    function __toString()
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
     * Вернет список полей
     * @abstract
     * @return array
     */
    abstract public function getFields();

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

}

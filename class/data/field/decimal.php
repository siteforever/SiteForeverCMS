<?php
/**
 * Поле типа DECIMAL
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Data_Field_Decimal extends Data_Field
{

    /**
     * Проверит значение на правильность
     * @var mixed $value Значение
     * @return void
     */
    function validate($value)
    {
        return filter_var( $value, FILTER_VALIDATE_FLOAT );
    }

    /**
     * Вернет строку для вставки в SQL запрос
     * @return string
     */
    function toString()
    {
        return "`{$this->name}` decimal({$this->length})".
                (!$this->null ? " NOT NULL" : "").
                (is_null($this->default)? "" : " DEFAULT '{$this->default}'");
    }

    function __construct($name, $length = '13,2', $notnull = false, $default = null)
    {
        parent::__construct($name, $length, $notnull, $default);
    }
}

<?php
/**
 * Поле типа INT
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Field_Int extends Data_Field
{

    /**
     * Проверит значение на правильность
     * @var mixed $value Значение
     * @return void
     */
    function validate($value)
    {
        return filter_var( $value, FILTER_VALIDATE_INT );
    }

    /**
     * Вернет строку для вставки в SQL запрос
     * @return string
     */
    function toString()
    {
        return "`{$this->name}` int({$this->length})".
                (!$this->null ? " NOT NULL" : "").
                (is_null($this->default)? "" : " DEFAULT '{$this->default}'").
                ($this->autoincrement ? " AUTO_INCREMENT" : "");
    }
}

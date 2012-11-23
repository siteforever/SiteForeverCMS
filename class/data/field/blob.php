<?php
/**
 * Поле типа Blob
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Data_Field_Blob extends Data_Field
{
    /**
     * Проверит значение на правильность
     * @var string $value Значение
     * @return string
     */
    function validate($value)
    {
        return filter_var( $value, FILTER_DEFAULT );
    }

    /**
     * Вернет строку для вставки в SQL запрос
     * @return string
     */
    function toString()
    {
        return "`{$this->name}` text";
    }
}

<?php
/**
 * Поле типа DECIMAL
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\AbstractDataField;

class DecimalField extends AbstractDataField
{

    /**
     * Проверит значение на правильность
     * @var mixed $value Значение
     * @return mixed
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

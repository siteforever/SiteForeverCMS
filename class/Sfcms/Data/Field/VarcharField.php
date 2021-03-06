<?php
/**
 * Поле типа VarcharField
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\AbstractDataField;

class VarcharField extends AbstractDataField
{
    /**
     * Проверит значение на правильность
     * @var mixed $value Значение
     * @return mixed
     */
    function validate($value)
    {
        return filter_var( $value, FILTER_SANITIZE_STRING );
    }

    /**
     * Вернет строку для вставки в SQL запрос
     * @return string
     */
    function toString()
    {
        $this->length = $this->length > 250 ? 250 : $this->length;
        return "`{$this->name}` varchar({$this->length})".
                (!$this->null ? " NOT NULL" : "").
                (is_null($this->default)? "" : " DEFAULT '{$this->default}'");
    }
}

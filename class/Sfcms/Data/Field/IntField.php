<?php
/**
 * Поле типа INT
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\AbstractField;
use Sfcms\Data\Field;

class IntField extends AbstractField
{

    /**
     * Проверит значение на правильность
     * @var mixed $value Значение
     * @return mixed
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

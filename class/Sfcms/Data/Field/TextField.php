<?php
/**
 * Поле типа TextField
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\AbstractDataField;

class TextField extends AbstractDataField
{
    protected $length = 65535;

    /**
     * Проверит значение на правильность
     * @var mixed $value Значение
     * @return mixed
     */
    function validate($value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * Вернет строку для вставки в SQL запрос
     * @return string
     */
    function toString()
    {
        return sprintf('`%s` text', $this->name);
    }
}

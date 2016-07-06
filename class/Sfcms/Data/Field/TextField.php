<?php
/**
 * Поле типа Text
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\AbstractField;
use Sfcms\Data\Field;

class TextField extends AbstractField
{
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

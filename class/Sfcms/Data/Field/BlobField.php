<?php
/**
 * Поле типа BlobField
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\AbstractDataField;

class BlobField extends AbstractDataField
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

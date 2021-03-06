<?php
/**
 * Поле даты-времени
 * @author: keltanas
 * @link  http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\AbstractDataField;

class DatetimeField extends AbstractDataField
{
    /**
     * Вернет строку для вставки в SQL запрос
     * @return string
     */
    public function toString()
    {
        return "`{$this->name}` DATETIME";
    }

    /**
     * Проверит значение на правильность
     * @var mixed $value Значение
     * @return bool
     */
    public function validate( $value )
    {
        return preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value) ? $value : false;
    }

}

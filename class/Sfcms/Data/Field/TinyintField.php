<?php
/**
 * Поле типа TINYINT
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data\Field;

use Sfcms\Data\Field;

class TinyintField extends IntField
{
    /**
     * Вернет строку для вставки в SQL запрос
     * @return string
     */
    function toString()
    {
        $this->length   = $this->length > 4 ? 4 : $this->length;
        return "`{$this->name}` tinyint({$this->length})".
                (!$this->null ? " NOT NULL" : "").
                (is_null($this->default)? "" : " DEFAULT '{$this->default}'").
                ($this->autoincrement ? " AUTO_INCREMENT" : "");
    }
}

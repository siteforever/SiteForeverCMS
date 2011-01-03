<?php
/**
 * Поле типа TINYINT
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Data_Field_Tinyint extends Data_Field_Int
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

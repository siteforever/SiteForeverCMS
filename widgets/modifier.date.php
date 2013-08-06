<?php
/**
 * Smarty plugin
 * Модификатор конвертации даты
 * -------------------------------------------------------------
 * Файл:     modifier.date.php
 * Тип:      modifier
 * Имя:      date
 * Назначение:  Конвертирует дату в нужный формат
 * -------------------------------------------------------------
 */
function smarty_modifier_date( $content, $format = 'c' )
{
    if (is_string($content)) {
        $content = new DateTime($content);
    }
    return $content->format($format);
}

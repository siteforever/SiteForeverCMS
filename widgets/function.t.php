<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Файл:     function.t.php
 * Тип:      function
 * Имя:      t
 * Назначение:  Переведет текст
 * -------------------------------------------------------------
 * Использование: {t text="some text"}
 */
function smarty_function_t($params, $smarty)
{
    return t( array_pop( $params ) );
}
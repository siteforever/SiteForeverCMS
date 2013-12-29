<?php
/**
 * Smarty plugin
 * Модификатор подсветки слов
 * -------------------------------------------------------------
 * Файл:     modifier.hl.php
 * Тип:      modifier
 * Имя:      hl
 * Назначение:  Подсветит слова из массива в параметре
 * -------------------------------------------------------------
 */
function smarty_modifier_hl( $content, $words = array() )
{
    if ( is_string( $words ) ) {
        $words  = array($words);
    }
    foreach ( $words as $word ) {
        if ( strlen( $word ) > 3 ) {
            // str_ireplace не берет русские символы
            $content = preg_replace( '@('.$word.')@ui', '<b class="highlight">$1</b>', $content );
        }
    }
    return $content;
}
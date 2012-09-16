<?php
/**
 * Создаст конструкцию тэгов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
function smarty_block_tabs( $params, $content )
{
    if( ! $content ) {
        $result = array('<ul class="nav nav-tabs">');
        $first = true;
        foreach ( $params as $key => $val ) {
            $result[] = "<li".($first?' class="active"':'')."><a href=\"#{$key}\" data-toggle=\"tab\">{$val}</a></li>";
            $first = false;
        }
        $result[] = '</ul>';
        return implode('',$result).'<div class="tab-content">';
    }
    return $content.'</div>';
}
<?php
/**
 * Создаст тэг ссылки
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
function smarty_block_a( $params, $content )
{
    $a  = array();
    if ( isset($params['href']) ) {
        $href    = $params['href'];
        unset( $params['href'] );
    } else {
        $href    = null;
    }

    if ( isset( $params['class'] ) ) {
        if ( is_string($params['class']) ) {
            $a['class'] = "class=\"{$params['class']}\"";
        }
        if ( is_array($params['class']) ) {
            $a['class'] = 'class="'.implode(' ', $params['class']).'"';
        }
        unset($params['class']);
    }

    $a['href']  = Siteforever::html()->href( $href, $params );

    $result = '<a '.implode(' ', $a).'>'.$content.'</a>';
    return  $result;
}
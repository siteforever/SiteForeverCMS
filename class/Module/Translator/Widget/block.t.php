<?php
/**
 * Перевод текста по словарю
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

function smarty_block_t( $params, $content, $smarty )
{
    $cat = null;
    if ( isset( $params['cat'] ) ) {
        $cat = $params['cat'];
        unset( $params['cat'] );
    }
    foreach ( $params as $key => $val ) {
        unset( $params[$key] );
        $params[ ':' . $key ] = $val;
    }

    if( $content ) {
        return Sfcms::i18n()->write( $cat, $content, $params );
    }
}

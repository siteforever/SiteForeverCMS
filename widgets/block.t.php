<?php
/**
 * Перевод текста по словарю
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
function smarty_block_t( $params, $content, $smarty )
{
    if ( $content ) {
        return translate::getInstance()->write( $content );
    }
}
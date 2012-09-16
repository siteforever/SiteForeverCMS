<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     block.tab.php
* Type:     block
* Name:     tab
* Purpose:  Одиночный таб
* -------------------------------------------------------------
*/
function smarty_block_tab( $params, $content )
{
    if ( !$content ) {
        return '<div class="tab-pane'.(isset($params['active'])?' active':'').'" id="'.$params['name'].'">';
    }
    return $content.'</div>';
}
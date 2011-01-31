<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.breadcrumbs.php
 * Type:     function
 * Name:     breadcrumbs
 * Purpose:  Напечатает "хлебные крошки"
 * -------------------------------------------------------------
 * @example {breadcrumbs path=$page.path}
 */
function smarty_function_breadcrumbs( $params, &$smarty )
{
    if ( isset ( $params['page'] ) && isset( $params['page']['path'] ) ) {
        $params['path'] = $params['page']['path'];
    }

    if ( !isset( $params['path'] ) ) {
        return '';
    }

    $html = array();
    if ( $patches = json_decode( $params['path'], true ) ) {
        if ( count($patches) > 0 ) {
            foreach( $patches as $path ) {
                $html[] = "<a ".href($path['url']).">{$path['name']}</a>";
            }
            return '<div class="b-breadcrumbs">'.join(' &gt; ', $html).'</div>';
        }
    }
}
<?php
/**
 * Smarty plugin BreadCrumbs
 * -------------------------------------------------------------
 * File:     function.breadcrumbs.php
 * Type:     function
 * Name:     breadcrumbs
 * Purpose:  Напечатает "хлебные крошки"
 * -------------------------------------------------------------
 * @example {breadcrumbs path=$page}
 */

function smarty_function_breadcrumbs( $params, Smarty_Internal_Template $template )
{
    $breadcrumbs    = App::getInstance()->getTpl()->getBreadcrumbs();
    if ( isset( $params['separator'] ) ) {
        $breadcrumbs->setSeparator($params['separator']);
    }
    return $breadcrumbs->render();
}

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
    $breadcrumbs    = App::getInstance()->getView()->getBreadcrumbs();
    if ( isset( $params['separator'] ) ) {
        $breadcrumbs->setSeparator($params['separator']);
    }
    $result = '<div class="b-breadcrumbs">'.$breadcrumbs->render().'</div>';
    return $result;
}
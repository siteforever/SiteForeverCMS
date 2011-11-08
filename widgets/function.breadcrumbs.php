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
    if ( isset ( $params['page'] ) && isset( $params['page']['path'] ) ) {
        $params['path'] = $params['page']['path'];
    }

    $breadcrumbs    = App::getInstance()->getView()->getBreadcrumbs();

    if ( isset( $params['separator'] ) ) {
        $breadcrumbs->setSeparator($params['separator']);
    }

    if ( isset( $params['path'] ) ) {
        $breadcrumbs->fromJson( $params['path'] );
    }

    $result = '<div class="b-breadcrumbs">'.$breadcrumbs->render().'</div>';
//    print microtime(1) - $start;
    return $result;
}
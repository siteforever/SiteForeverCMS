<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.menu.php
* Type:     function
* Name:     menu
* Purpose:  Выведет меню на сайте
* -------------------------------------------------------------
*/
function smarty_function_menu($params, Smarty_Internal_Template $smarty)
{
    $parent = isset( $params['parent'] ) ? $params['parent'] : 0;
    $level  = isset( $params['level'] ) ? $params['level'] : 0;
    $template = isset( $params['template'] ) ? $params['template'] : 'menu';
    $source = isset( $params['source'] ) ? $params['source'] : 'widget';

    /** @var $model Model_Page */
    $model  = Sfcms_Model::getModel('Page');

    if ( ! count( $model->parents ) ) {
        $model->createParentsIndex();
    }
    $smarty->assign('parent', $parent );
    $smarty->assign('level', $level );
    $smarty->assign('currentId', App::getInstance()->getRequest()->get('id') );

    $smarty->assign('parents', $model->parents);

    return $smarty->fetch("{$source}:{$template}.tpl");
}

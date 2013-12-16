<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.admin.php
* Type:     function
* Name:     admin
* Purpose:  Панель администратора
* -------------------------------------------------------------
*/
function smarty_function_admin($params, Smarty_Internal_Template $template)
{
    if ( ! App::cms()->getAuth()->hasPermission(USER_ADMIN) ) {
        return '';
    }
    return '';
    return $template->fetch('system:admin/panel.tpl');
}

<?php
/*
 * Smarty plugin
 *
 * -------------------------------------------------------------
 * File:     function.login.php
 * Type:     function
 * Name:     login
 * Purpose:  Выведет форму входа пользователя
 * -------------------------------------------------------------
 *
 * @example {login}
 *
 */
function smarty_function_login($params, $smarty)
{
    $app    = App::getInstance();
    $tpl    = $app->getTpl();
    $user   = $app->getAuth()->currentUser();

    $tpl->assign('form', Sfcms_Model::getModel('User')->getLoginForm() );
    $tpl->assign('user', $user->getAttributes() );
    if ( $user->perm == USER_GUEST )
    {
        $tpl->assign('auth', '1');
    }
    else {
        $tpl->assign('auth', '0');
    }

    return $tpl->fetch('users.head_login');;
}
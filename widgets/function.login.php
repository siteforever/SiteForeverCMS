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
    App::$tpl->assign('form', App::$user->getLoginForm() );
    App::$tpl->assign('user', App::$user->getData() );
    if ( App::$user->getPermission() == USER_GUEST )
    {
        App::$tpl->assign('auth', '1');
    }
    else {
        App::$tpl->assign('auth', '0');
    }
    
    return App::$tpl->fetch('system:users.head_login');;
    
    //printVar(App::$user);
    if ( App::$user->getPermission() == USER_GUEST ) {
        $form = App::$user->getLoginForm();
        App::$tpl->assign('form', $form);
        App::$tpl->assign('auth', 0);
    }
    else {
        App::$tpl->assing('user', App::$user->get('login') );
        App::$tpl->assign('auth', 1);
    }
    return '';//App::$tpl->fetch('system:users.head_login');
}
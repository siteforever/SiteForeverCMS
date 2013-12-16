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
    $app    = App::cms();
    $tpl    = $app->getTpl();

    $tpl->assign('form', \Sfcms\Model::getModel('User')->getLoginForm());
    $tpl->assign('user', $app->getAuth()->currentUser());
    $tpl->assign('auth', (bool)$app->getAuth()->getId());

    return $tpl->fetch('user.head_login');
}

<?php
/**
 * Форма логина на сайте
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Forms\User;
 
class Login extends \Form_Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name'      => 'login',
            'action'    => \App::$router->createLink('users/login'),
            'fields'    => array(
                'login'     => array('type'=>'text',    'label'=>'Логин',   'required'),
                'password'  => array('type'=>'password','label'=>'Пароль',  'required'),
                'submit'    => array('type'=>'submit', 'value'=>'Войти',),
            ),
            'buttons'   => array(
                'submit'    => array('type'=>'submit', 'value'=>'bВойти',),
            ),
        ));
    }
}

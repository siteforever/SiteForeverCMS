<?php
/**
 * Форма логина на сайте
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Forms\User;

use Sfcms\Form\Form;

class Login extends Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name'      => 'login',
            'action'    => \App::cms()->getRouter()->createLink('user/login'),
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

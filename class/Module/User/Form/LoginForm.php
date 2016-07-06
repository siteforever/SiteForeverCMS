<?php
/**
 * Форма логина на сайте
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Module\User\Form;

use Sfcms\Form\Form;

class LoginForm extends Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name'      => 'login',
            'action'    => \App::cms()->get('router')->generate('user/login'),
            'class'     => '',
            'fields'    => array(
                'login'     => array('type'=>'text',    'label'=>'Логин',   'required'),
                'password'  => array('type'=>'password','label'=>'Пароль',  'required'),
                'submit'    => array('type'=>'submit', 'value'=>'Войти',),
            ),
        ));
    }
}

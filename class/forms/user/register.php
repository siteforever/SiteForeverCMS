<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class forms_user_register extends form_Form
{
    function __construct()
    {
        return parent::__construct(array(
            'name'      => 'register',
            'class'     => 'standart',
            'fields'    => array(
                'email'     => array('type'=>'text',    'label'=>'Email', 'required', 'autocomplete'=>'off'),
                'login'     => array('type'=>'text',    'label'=>'Логин', 'required', 'autocomplete'=>'off'),
                'password'  => array('type'=>'password', 'label'=>'Пароль', 'required', 'autocomplete'=>'off'),
                'captcha'   => array('type'=>'captcha', 'label'=>'Проверка'),
                'submit'    => array('type'=>'submit', 'value'=>'Регистрация'),
            ),
        ));
    }
}

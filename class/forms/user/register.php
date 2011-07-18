<?php
/**
 * Форма регистрации нового пользователя
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Forms_User_Register extends Form_Form
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

                'fname'     => array('label'=>'Имя'),
                'lname'     => array('label'=>'Фамилия'),
                'phone'     => array('label'=>'Телефон'),

                'captcha'   => array('type'=>'captcha', 'label'=>'Проверка'),
                'submit'    => array('type'=>'submit', 'value'=>'Регистрация'),
            ),
            'buttons'   => array(
                'submit'    => array('type'=>'submit', 'value'=>'bРегистрация'),
            ),
            'validate'  => array(
                'email'     => array('email'),
                'login'     => array('minlength'=>6),
                'password'  => array('minlength'=>6),
            ),
        ));
    }
}

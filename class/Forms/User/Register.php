<?php
/**
 * Форма регистрации нового пользователя
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
use Sfcms\Form\Form;

class Forms_User_Register extends Form
{
    function __construct()
    {
        return parent::__construct(array(
            'name'      => 'register',
            'class'     => 'form-horizontal',
            'fields'    => array(
                'email'     => array('type'=>'text',    'label'=>'Email', 'required',
                                     'filter' => 'email', 'autocomplete'=>'off'),
                'login'     => array('type'=>'text',    'label'=>'Логин', 'required',
                                     'notice'=>'Минимум 5 символов',  'autocomplete'=>'off'),
                'password'  => array('type'=>'password', 'label'=>'Пароль', 'required', 'autocomplete'=>'off'),

                'fname'     => array('label'=>'Имя'),
                'lname'     => array('label'=>'Фамилия'),
                'phone'     => array('label'=>'Телефон', 'filter' => 'phone', 'notice' => '+7 900 123 45 67',),

                'captcha'   => array('type'=>'captcha', 'label'=>'Проверочный код'),
                'submit'    => array('type'=>'submit', 'value'=>'Регистрация'),
            ),
            'validate'  => array(
                'email'     => array('email'),
                'login'     => array('minlength'=>6),
                'password'  => array('minlength'=>6),
            ),
        ));
    }
}

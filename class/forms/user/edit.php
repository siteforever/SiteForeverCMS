<?php
/**
 * Created by PhpStorm.
 * User: keltanas
 * Date: 09.10.2010
 * Time: 1:47:15
 * To change this template use File | Settings | File Templates.
 */
 
class forms_user_edit extends form_Form
{
    function __construct()
    {
        return  parent::__construct(array(
            'name'      => 'users',
            'fields'    => array(
                'id'        => array('type'=>'hidden', 'value'=>'0'),
                'login'     => array('type'=>'text', 'label'=>'Логин', 'required'),
                'password'  => array('type'=>'password', 'label'=>'Пароль',),
                'solt'      => array('type'=>'hidden', 'value'=>''),
                'fname'     => array('type'=>'text', 'label'=>'Имя',),
                'lname'     => array('type'=>'text', 'label'=>'Фамилия',),
                'email'     => array('type'=>'text', 'label'=>'Email', 'required'),
                'name'      => array('type'=>'text', 'label'=>'Наименование организации'),
                'phone'     => array('type'=>'text', 'label'=>'Телефон',),
                'fax'       => array('type'=>'text', 'label'=>'Факс',),
                'inn'       => array('type'=>'text', 'label'=>'ИНН',),
                'kpp'       => array('type'=>'text', 'label'=>'КПП',),
                'address'   => array('type'=>'textarea', 'label'=>'Адрес', 'class'=>'plain', 'height'=>'100'),
                'status'    => array(
                        'type'      => 'radio',
                        'variants'  => array(1 => 'Включен', 0 => 'Выключен'),
                        'label'     => 'Статус',
                        'value'     => '0',
                ),
                'date'      => array('type'=>'date', 'label'=>'Дата регистрации',),
                'last'      => array('type'=>'date', 'label'=>'Последний вход',),
                'perm'      => array(
                        'type'      => 'select',
                        'variants'  => App::$config->get('users.groups'),
                        'label'     => 'Группа',
                        'value'     => '3'
                ),
                'confirm'   => array('type'=>'hidden'),
                'basket'    => array('type'=>'hidden'),
                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}

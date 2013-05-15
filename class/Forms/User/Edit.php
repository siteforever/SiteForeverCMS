<?php
/**
 * Форма редактирования пользователя в админке
 * @author E.Nikolay <keltanas@gmail.com>
 */
use Sfcms\Model;
use Sfcms\Form\Form;

class Forms_User_Edit extends Form
{
    public function __construct()
    {
        return  parent::__construct(array(
            'name'      => 'user',
            'action'    => App::getInstance()->getRouter()->createServiceLink('user','save'),
            'fields'    => array(
                'id'        => array('type'=>'hidden', 'value'=>'0'),
                'login'     => array('type'=>'text', 'label'=>'Логин', 'required'),
                'password'  => array('type'=>'password', 'label'=>'Пароль',),
                'solt'      => array('type'=>'hidden', 'value'=>''),
                'fname'     => array('type'=>'text', 'label'=>'Имя',),
                'lname'     => array('type'=>'text', 'label'=>'Фамилия',),
                'email'     => array('type'=>'text', 'label'=>'Email', 'filter'=>'email', 'required'),
                'name'      => array('type'=>'text', 'label'=>'Наименование организации'),
                'phone'     => array('type'=>'text', 'label'=>'Телефон','filter'=>'phone',),
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
                        'variants'  => Model::getModel('User')->getGroups(),
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

<?php
/**
 * Форма редактирования пользователя в админке
 * @author E.Nikolay <keltanas@gmail.com>
 */
namespace Module\User\Form;

use Sfcms\Model;
use Sfcms\Form\Form;

class UserEditForm extends Form
{
    public function __construct()
    {
        return  parent::__construct(array(
            'name'      => 'user',
            'action'    => \App::cms()->getRouter()->createServiceLink('user','save'),
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
                'address'   => array('type'=>'textarea', 'label'=>'Адрес', 'class'=>'plain'),
                'status'    => array(
                        'type'      => 'checkbox',
                        'label'     => 'Включен',
                ),
                'date'      => array('type'=>'date', 'label'=>'Дата регистрации',),
                'last'      => array('type'=>'date', 'label'=>'Последний вход',),
                'perm'      => array(
                        'type'      => 'select',
                        'variants'  => \App::cms()->getDataManager()->getModel('User')->getGroups(),
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

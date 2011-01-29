<?php
/**
 * Форма редактирования профиля
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class forms_user_profile extends form_Form
{
    function __construct()
    {
        return parent::__construct(array(
            'name'      => 'profile',
            'class'     => 'standart',
            'fields'    => array(
                'id'        => array('type'=>'hidden',),
                'fname'     => array('type'=>'text', 'label'=>'Имя'),
                'lname'     => array('type'=>'text', 'label'=>'Фамилия'),
                'email'     => array('type'=>'text', 'label'=>'Email', 'required'),
                'name'      => array('type'=>'text', 'label'=>'Наименование организации **'),
                'phone'     => array('type'=>'text', 'label'=>'Телефон **'),
                'fax'       => array('type'=>'text', 'label'=>'Факс',),
                'inn'       => array('type'=>'text', 'label'=>'ИНН **',),
                'kpp'       => array('type'=>'text', 'label'=>'КПП **',),
                'address'   => array('type'=>'textarea', 'label'=>'Адрес'),

                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}

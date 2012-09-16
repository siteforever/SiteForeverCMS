<?php
/**
 * Форма редактирования профиля
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Forms_User_Profile extends Form_Form
{
    function __construct()
    {
        return parent::__construct(array(
            'name'      => 'profile',
            'class'     => 'form-horizontal ajax-validate',
            'fields'    => array(
                'id'        => array('type'=>'hidden',),
                'fname'     => array('type'=>'text', 'label'=>'Имя'),
                'lname'     => array('type'=>'text', 'label'=>'Фамилия'),
                'email'     => array('type'=>'text', 'label'=>'Email', 'filter'=>'email', 'required'),
                'phone'     => array('type'=>'text', 'label'=>'Телефон', 'filter'=>'phone', 'required'),
                'address'   => array('type'=>'textarea', 'label'=>'Адрес'),
//                'hr',
//                'name'      => array('type'=>'text', 'label'=>'Наименование организации **'),
//                'fax'       => array('type'=>'text', 'label'=>'Факс **',),
//                'inn'       => array('type'=>'text', 'label'=>'ИНН **',),
//                'kpp'       => array('type'=>'text', 'label'=>'КПП **',),

                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}

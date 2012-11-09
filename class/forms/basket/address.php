<?php
/**
 * Форма редактирования профиля
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Forms_Basket_Address extends Form_Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name'      => 'basket_address',
            'class'     => 'form-horizontal ajax-validate',
            'action'    => App::getInstance()->getRouter()->createServiceLink('basket','index'),
            'fields'    => array(
                'delivery_id'  => array(
                    'type'      =>'radio',
                    'required',
                    'label'     =>'Доставка',
                    'value'     => filter_input( $_SESSION['delivery'], FILTER_SANITIZE_NUMBER_INT ) ?: '0',
                    'variants'  => array(),
                ),
                'payment_id' => array(
                    'type'      => 'radio',
                    'required',
                    'label'     => t('Payment'),
                    'value'     => '0',
                    'variants'  => array(),
                ),
                'fname'     => array('type'=>'text', 'label'=>'Имя', 'required'),
                'lname'     => array('type'=>'text', 'label'=>'Фамилия'),
                'email'     => array('type'=>'text', 'label'=>'Email', 'filter'=>'email', 'required'),
                'phone'     => array('type'=>'text', 'label'=>'Телефон',
                                     'filter'=>'phone', 'notice'=>'Формат: +7 812 123 45 67', 'required'),
                'country'   => array('type'=>'text', 'label'=>'Страна', 'required'),
                'city'      => array('type'=>'text', 'label'=>'Город', 'required'),
                'address'   => array('type'=>'textarea', 'label'=>'Адрес', 'required'),
                'zip'       => array('type'=>'text', 'label'=>'Индекс', 'required'),

                'comment'   => array('type'=>'textarea', 'label'=>'Комментарий',),

                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}

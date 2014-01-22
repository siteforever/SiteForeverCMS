<?php
/**
 * Форма редактирования профиля
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Module\Market\Form;

use Sfcms\Form\Field\Checkbox;
use Sfcms\Form\Form;
use Sfcms\Router;

class OrderForm extends Form
{
    public function __construct()
    {
        parent::__construct(array(
            'name'      => 'order',
            'class'     => 'ajax-validate',
//            'action'    => $this->getRouter()->createServiceLink('basket','index'),
            'fields'    => array(
                'person' => array(
                    'type' => 'radio',
                    'label' => 'Заказчик',
                    'value' => '0',
                    'variants' => array(
                        '0' => 'Физическое лицо',
                        '1' => 'Юридическое лицо',
                    ),
                ),

                'delivery_id'  => array(
                    'type'      =>'radio',
                    'required',
                    'label'     =>'Доставка',
                    'value'     => '0',
                    'variants'  => array(),
                ),

                'payment_id' => array(
                    'type'      => 'radio',
                    'required',
                    'label'     => 'Payment',
                    'value'     => 1,
                    'variants'  => array(),
                ),

                'fname'     => array('type'=>'text', 'label'=>'Имя', 'required'),
                'lname'     => array('type'=>'text', 'label'=>'Фамилия'),
                'email'     => array('type'=>'text', 'label'=>'Email', 'filter'=>'email', 'required'),
                'phone'     => array('type'=>'text', 'label'=>'Телефон',
                                     'filter'=>'phone', 'notice'=>'Формат: +7 812 123 45 67', 'required'),

                'zip'       => array('type'=>'text', 'label'=>'Индекс', 'filter'=>'/\d{1,10}/'),

                'country'   => array(
                    'type'=>'text',
                    'label'=>'Страна',
                ),
                'region'      => array(
                    'type'=>'text',
                    'label'=>'Область',
                ),
                'city'      => array(
                    'type'=>'text',
                    'label'=>'Город',
                ),
                'metro'     => array(
                    'type'  => 'select',
                    'label' => 'Метро',
                    'variants'  => array(),
                    'notice'    => 'Укажите ближайшую к вам станцию',
                ),

                'address'   => array(
                    'type'  => 'textarea',
                    'label' => 'Адрес',
                    'notice' => 'ул. Кораблестроителей, д.59, к.2, кв.103, домофон: 1568, и т.д.',
                ),

                'details'   => array(
                    'type'  => 'textarea',
                    'label' => 'Реквизиты организации',
                ),

                'passport'   => array(
                    'type'  => 'textarea',
                    'label' => 'Паспортные данные',
                    'notice' => 'Фамилия, Имя, Отчество, Серия, Номер, Кем и Когда выдан',
                ),

                'comment'   => array('type'=>'textarea', 'label'=>'Комментарий',),

                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}

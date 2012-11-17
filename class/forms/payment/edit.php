<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Forms\Payment;

use Form_Form;

class Edit extends Form_Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name' => 'PaymentEdit',
            'action' => \App::getInstance()->getRouter()->createServiceLink('payment','edit'),
            'fields' => array(
                'id' => array(
                    'type' => 'hidden',
                ),
                'name' => array(
                    'type' => 'text',
                    'label' => t('Name'),
                    'required',
                ),
                'desc' => array(
                    'type' => 'textarea',
                    'label' => t('Desc'),
                    'require',
                ),
                'module' => array(
                    'type' => 'select',
                    'label' => t('Module'),
                    'value' => '0',
                    'variants' => array('basket'=>'Корзина','robokassa'=>'Робокасса'),
                    'require',
                ),
                'active' => array(
                    'type' => 'radio',
                    'label' => t('Active'),
                    'value' => '1',
                    'variants' => array(t('No'),t('Yes')),
                    'require',
                ),
            ),
        ));
    }
}

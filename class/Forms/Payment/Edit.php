<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Forms\Payment;

use Sfcms\Form\Form;

class Edit extends Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name' => 'PaymentEdit',
            'action' => \Sfcms::html()->url('payment/edit'),
            'fields' => array(
                'id' => array(
                    'type' => 'hidden',
                ),
                'name' => array(
                    'type' => 'text',
                    'label' => $this->t('Name'),
                    'required',
                ),
                'desc' => array(
                    'type' => 'textarea',
                    'label' => $this->t('Desc'),
                    'require',
                ),
                'module' => array(
                    'type' => 'select',
                    'label' => $this->t('Module'),
                    'value' => '0',
                    'variants' => array('basket'=>'Корзина','robokassa'=>'Робокасса'),
                    'require',
                ),
                'active' => array(
                    'type' => 'radio',
                    'label' => $this->t('Active'),
                    'value' => '1',
                    'variants' => array($this->t('No'),$this->t('Yes')),
                    'require',
                ),
            ),
        ));
    }
}

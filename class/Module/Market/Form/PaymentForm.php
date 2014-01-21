<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Module\Market\Form;

use Sfcms\Form\Form;

class PaymentForm extends Form
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
                    'label' => 'Name',
                    'required',
                ),
                'desc' => array(
                    'type' => 'textarea',
                    'label' => 'Desc',
                    'require',
                ),
                'module' => array(
                    'type' => 'select',
                    'label' => 'Module',
                    'value' => '0',
                    'variants' => array('basket'=>'Basket','robokassa'=>'Robokassa'),
                    'require',
                ),
                'active' => array(
                    'type' => 'checkbox',
                    'label' => 'Active',
                    'value' => '1',
                ),
            ),
        ));
    }
}

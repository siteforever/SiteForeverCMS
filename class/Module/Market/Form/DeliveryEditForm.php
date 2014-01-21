<?php
/**
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Market\Form;

/**
 * Delivery edition
 */
class DeliveryEditForm extends \Sfcms\Form\Form
{
    public function __construct()
    {
        parent::__construct( array(
            'name' => 'DeliveryEdit',
            'action' => \App::cms()->getRouter()->createServiceLink('delivery','edit'),
            'fields' => array(
                'id' => array('type'=>'int','hidden'),
                'name' => array('type'=>'text','label'=>'Name'),
                'desc' => array('type'=>'textarea','label'=>'Desc','class'=>'plain'),
                'cost' => array('type'=>'float','label'=>'Cost'),
                'active' => array(
                    'type'=>'radio','label'=>'Active',
                    'value' => '1', 'variants' => array('1' => 'Yes', '0' => 'No'),
                ),
            ),
        ) );
    }

}

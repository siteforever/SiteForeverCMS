<?php
/**
 * Привка доставки
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

class Forms_Delivery_Edit extends \Sfcms\Form\Form
{
    public function __construct()
    {
        parent::__construct( array(
            'name' => 'DeliveryEdit',
            'action' => App::getInstance()->getRouter()->createServiceLink('delivery','edit'),
            'fields' => array(
                'id' => array('type'=>'int','hidden'),
                'name' => array('type'=>'text','label'=>t('delivery','Name')),
                'desc' => array('type'=>'textarea','label'=>t('delivery','Desc'),'class'=>'plain'),
                'cost' => array('type'=>'float','label'=>t('delivery','Cost')),
                'active' => array(
                    'type'=>'radio','label'=>t('delivery','Active'),
                    'value' => '1', 'variants' => array('1' => t('Yes'), '0' => t('No')),
                ),
            ),
        ) );
    }

}

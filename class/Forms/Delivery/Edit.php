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
                'name' => array('type'=>'text','label'=>$this->t('delivery','Name')),
                'desc' => array('type'=>'textarea','label'=>$this->t('delivery','Desc'),'class'=>'plain'),
                'cost' => array('type'=>'float','label'=>$this->t('delivery','Cost')),
                'active' => array(
                    'type'=>'radio','label'=>$this->t('delivery','Active'),
                    'value' => '1', 'variants' => array('1' => $this->t('Yes'), '0' => $this->t('No')),
                ),
            ),
        ) );
    }

}

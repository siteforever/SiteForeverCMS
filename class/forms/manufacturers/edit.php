<?php
/**
 * Правка производителя
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Forms_Manufacturers_Edit extends Form_Form
{
    public function __construct()
    {
        parent::__construct(
            array(
                 'name' => 'manufacturers',
                 'action'    => '/?route=manufacturers/save',
                 'fields' => array(
                     'id'   => array( 'type'=>'hidden', ),
                     'name' => array( 'type'=>'text', 'label' => t('Name'), 'required', ),
                     'email' => array( 'type'=>'text', 'label' => t('Email') ),
                     'phone' => array( 'type'=>'text', 'label' => t('Phone') ),
                     'address' => array( 'type'=>'textarea', 'label' => t('Address'), 'class' => 'plain' ),
                     'image' => array( 'type'=>'text', 'label' => t('Image'), 'class'=>'image',
                                       'notice'=>t('Double-click to select the image')
                                ),
                     'description' => array( 'type'=>'textarea', 'label' => t('Description'), ),

                     'submit'    => array( 'type'=>'submit', 'value'=>t('Save') ),
                 ),
            )
        );
    }

}

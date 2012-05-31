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
                     'name' => array( 'type'=>'text', 'label' => t('Name') ),
                     'email' => array( 'type'=>'text', 'label' => t('Email') ),
                     'phone' => array( 'type'=>'text', 'label' => t('Phone') ),

                     'submit'    => array( 'type'=>'submit', 'value'=>t('Save') ),
                 ),
            )
        );
    }

}

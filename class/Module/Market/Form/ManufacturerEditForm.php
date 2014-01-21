<?php
/**
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

namespace Module\Market\Form;

class ManufacturerEditForm extends \Sfcms\Form\Form
{
    public function __construct()
    {
        // @todo Не работает javascript модуль в админке
        parent::__construct(
            array(
                 'name' => 'manufacturers',
                 'action'    => \App::cms()->getRouter()->createLink('manufacturers/save'),
                 'fields' => array(
                     'id'   => array('type'=>'hidden'),
                     'name' => array('type'=>'text', 'label' => 'Name', 'required'),
                     'email' => array('type'=>'text', 'label' => 'Email', 'filter' => 'email'),
                     'site'  => array('type'=>'text', 'label' => 'Site', 'filter' => 'url'),
                     'phone' => array('type'=>'text', 'label' => 'Phone', 'filter' => 'phone'),
                     'address' => array('type'=>'textarea', 'label' => 'Address', 'class' => 'plain'),
                     'image' => array('type'=>'text', 'label' => 'Image', 'class'=>'image',
                                       'notice'=>'Double-click to select the image'
                                ),
                     'description' => array('type'=>'textarea', 'label' => 'Description'),

                     'submit'    => array( 'type'=>'submit', 'value'=>'Save'),
                 ),
            )
        );
    }

}

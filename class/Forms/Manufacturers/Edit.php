<?php
/**
 * Правка производителя
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Forms_Manufacturers_Edit extends \Sfcms\Form\Form
{
    public function __construct()
    {
        parent::__construct(
            array(
                 'name' => 'manufacturers',
                 'action'    => '/?route=manufacturers/save',
                 'fields' => array(
                     'id'   => array( 'type'=>'hidden', ),
                     'name' => array( 'type'=>'text', 'label' => $this->t('Name'), 'required', ),
                     'email' => array( 'type'=>'text', 'label' => $this->t('Email'), 'filter' => 'email', ),
                     'site'  => array( 'type'=>'text', 'label' => $this->t('Site'), 'filter' => 'url', ),
                     'phone' => array( 'type'=>'text', 'label' => $this->t('Phone'), 'filter' => 'phone' ),
                     'address' => array( 'type'=>'textarea', 'label' => $this->t('Address'), 'class' => 'plain' ),
                     'image' => array( 'type'=>'text', 'label' => $this->t('Image'), 'class'=>'image',
                                       'notice'=>$this->t('Double-click to select the image')
                                ),
                     'description' => array( 'type'=>'textarea', 'label' => $this->t('Description'), ),

                     'submit'    => array( 'type'=>'submit', 'value'=>$this->t('Save') ),
                 ),
            )
        );
    }

}

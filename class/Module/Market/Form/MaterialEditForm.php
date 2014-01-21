<?php
/**
 * Форма правки материала
 * @author: keltanas
 * @link http://siteforever.ru
 */
namespace Module\Market\Form;

use Sfcms;

class MaterialEditForm extends Sfcms\Form\Form
{
    public function __construct()
    {
        parent::__construct(
            array(
                'name' => 'material',
                'action'    => Sfcms::html()->url('material/save'),
                'class' => 'form-horizontal',
                'fields' => array(
                    'id'   => array( 'type'=>'hidden', ),
                    'name' => array( 'type'=>'text', 'label' => 'Name', 'required', ),
                    'image' => array( 'type'=>'text', 'label' => 'Image', 'class'=>'image',
                        'notice'=>'Double-click to select the image'
                    ),
                    'active' => array('type'=>'checkbox', 'label'=>'Active'),
                ),
            )
        );
    }
}

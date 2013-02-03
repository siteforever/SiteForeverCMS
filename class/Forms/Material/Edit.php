<?php
/**
 * Форма правки материала
 * @author: keltanas
 * @link http://siteforever.ru
 */
namespace Forms\Material;

use Form_Form;
use Sfcms;

class Edit extends Sfcms\Form\Form
{
    public function __construct()
    {
        parent::__construct(
            array(
                'name' => 'material',
                'action'    => Sfcms::html()->url('material/save'),
                'fields' => array(
                    'id'   => array( 'type'=>'hidden', ),
                    'name' => array( 'type'=>'text', 'label' => t('Name'), 'required', ),
                    'image' => array( 'type'=>'text', 'label' => t('Image'), 'class'=>'image',
                        'notice'=>t('Double-click to select the image')
                    ),
                    'active' => array('type'=>'radio', 'label'=>t('material','Active'), 'value'=>'1','variants'=>array( '1'=>t('Yes'), '0'=>t('No') )),

//                    'submit'    => array( 'type'=>'submit', 'value'=>t('Save') ),
                ),
            )
        );
    }
}

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
                    'name' => array( 'type'=>'text', 'label' => $this->t('Name'), 'required', ),
                    'image' => array( 'type'=>'text', 'label' => $this->t('Image'), 'class'=>'image',
                        'notice'=>$this->t('Double-click to select the image')
                    ),
                    'active' => array('type'=>'radio', 'label'=>$this->t('material','Active'), 'value'=>'1','variants'=>array( '1'=>$this->t('Yes'), '0'=>$this->t('No') )),

//                    'submit'    => array( 'type'=>'submit', 'value'=>t('Save') ),
                ),
            )
        );
    }
}

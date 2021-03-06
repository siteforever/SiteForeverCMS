<?php
/**
 * Form for editing picture gallery module
 */
namespace Module\Gallery\Form;

class ImageForm extends \Sfcms\Form\Form
{
    function __construct()
    {
        return parent::__construct(array(
            'name'      => 'gallery_picture',
            'action'    => \Sfcms::html()->url('gallery/edit'),
            'fields'    => array(
                'id'            => array('type'=>'int', 'hidden'),
                'name'          => array('label'=>'Наименование', 'type'=>'text'),
                'link'          => array('label'=>'Внешняя ссылка', 'type'=>'text'),
                'description'       => array('label'=>'Описание', 'type'=>'textarea'),
            ),
        ));
    }
}

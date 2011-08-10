<?php
/**
 * Form for editing picture gallery module
 */
class forms_gallery_image extends form_Form
{
    function __construct()
    {
        return parent::__construct(array(
            'name'      => 'gallery_picture',
            'action'    => '/?route=admin/gallery',
            'fields'    => array(
                'id'            => array('type'=>'int', 'hidden'),
                'name'          => array('label'=>'Наименование', 'type'=>'text'),
                'link'          => array('label'=>'Внешняя ссылка', 'type'=>'text'),
                'meta_description'  => array('type'=>'text',        'class'=>'elcatalog-meta',  'label'=>'Description',),
                'meta_keywords'     => array('type'=>'text',        'class'=>'elcatalog-meta',  'label'=>'Keywords',),
                'meta_h1'           => array('type'=>'text',        'class'=>'elcatalog-meta',  'label'=>'H1',),
                'meta_title'        => array('type'=>'text',        'class'=>'elcatalog-meta',  'label'=>'Title',),

                'description'   => array('label'=>'Описание', 'type'=>'textarea'),
            ),
        ));
    }
}

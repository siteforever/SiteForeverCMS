<?php
namespace Module\Banner\Form;
/**
 * Форма категорий баннеров
 */
class BannerCategoryForm extends \Sfcms\Form\Form
{
    function __construct()
    {
        parent::__construct(array(
            'name'      => 'CategoryBanner',
            'action'    => \App::cms()->getRouter()->createServiceLink('banner', 'savecat'),
            'fields'    => array(
                'id'                => array('type'=>'int', 'hidden'),
                'name'              => array(
                    'type' => 'text',
                    'class'=> 'elcatalog-meta',
                    'label'=> 'Name',
                    'required',
                ),
            ),
        ));
    }

}

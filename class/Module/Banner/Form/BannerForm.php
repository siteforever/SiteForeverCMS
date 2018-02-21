<?php
namespace Module\Banner\Form;

use Module\Banner\Model\CategoryModel;

/**
 * Форма для редактирования баннера
 */
class BannerForm extends \Sfcms\Form\Form
{
    public function __construct(CategoryModel $categoryModel)
    {
        $parents    = $categoryModel->getCategoryBanner();
        parent::__construct(array(
            'name'      => 'Banner',
            'action'    => \App::cms()->get('router')->generate('banner/save'),
            'fields'    => array(
                'id'                => array('type'=>'int', 'hidden'),
                'cat_id'    => array(
                            'type'      => 'select',
                            'label'     => 'Категория',
                            'variants'  => $parents,
                        ),
                'name'              => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>'Name',),
                'url'               => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>'Url',),
                'target'            => array(
                            'type'  => 'select',
                            'label' => 'Цель',
                            'variants'  => array(
//                                '_parent'   =>'Открыть в фрейм родителя',
                                '_blank'    =>'Открыть в новом окне',
                                '_self'     =>'Открыть в текущем окне',
//                                '_top'      =>'Отменяет фреймы и загружает в текущее окно',
                            ),
                ),
                'content'           => array('type'=>'textarea', 'label'=>'Content'),

//                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

}



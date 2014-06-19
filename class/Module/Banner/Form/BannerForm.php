<?php
namespace Module\Banner\Form;

use Sfcms\Data\DataManager;

/**
 * Форма для редактирования баннера
 */
class BannerForm extends \Sfcms\Form\Form
{
    public function __construct(DataManager $dataManager)
    {
        $parents    = $dataManager->getModel('CategoryBanner')->getCategoryBanner();
        parent::__construct(array(
            'name'      => 'Banner',
            'action'    => \App::cms()->getRouter()->createLink('banner/save'),
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



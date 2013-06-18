<?php
/**
 * Форма для редактирования баннера
 */

class Forms_Banners_Banner extends \Sfcms\Form\Form
{

    function __construct() {
        $parents    = \Sfcms\Model::getModel('CategoryBanner')->getCategoryBanner();
        parent::__construct(array(
            'name'      => 'Banner',
            'action'    => Sfcms::html()->url('banner/save'),
            'title'     => 'Настройка баннеров',
            'fields'    => array(
                'id'                => array('type'=>'int', 'hidden'),
                'cat_id'    => array(
                            'type'      => 'select',
                            'label'     => 'Категория',
                            'variants'  => $parents,
                        ),
                'name'              => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>$this->t('Name'),),
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
                'content'           => array('type'=>'textarea', 'label'=>$this->t('page','Content')),

//                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

}



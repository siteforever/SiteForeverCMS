<?php
/**
 * Форма для редактирования баннера
 */
 
class Forms_Banners_Banner extends Form_Form
{

    function __construct() {
        $parents    = Sfcms_Model::getModel('CategoryBanner')->getCategoryBanner();
        parent::__construct(array(
            'name'      => 'Banner',
            'action'    => App::getInstance()->getRouter()->createServiceLink('banner', 'save'),
            'title'     => 'Настройка баннеров',
            'fields'    => array(
                'id'                => array('type'=>'int', 'hidden'),
                'cat_id'    => array(
                            'type'      => 'select',
                            'label'     => 'Категория',
                            'variants'  => $parents,
                        ),
                'name'              => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>t('Name'),),
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
                'content'           => array('type'=>'textarea', 'label'=>t('page','Content')),

//                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

}



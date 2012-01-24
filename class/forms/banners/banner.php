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
            'action'    => App::getInstance()->getRouter()->createServiceLink('banner', 'edit'),
            'title'     => 'Настройка баннеров',
            'fields'    => array(
                'id'                => array('type'=>'int', 'hidden'),
                'cat_id'    => array(
                            'type'      => 'select',
                            'label'     => 'Название категории баннера',
                            'variants'  => $parents,
                        ),
                'name'              => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>'Название баннера',),
                'url'               => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>'Адрес перехода',),
//                'path'              => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>'Путь к картинке баннера',),
                'target'            => array(
                            'type'  => 'select',
                            'label' => 'Куда загружать ссылки',
                            'variants'  => array(
                                '_parent'   =>'Старница в фрейм родителя',
                                '_blank'    =>'Изображение в новом окне',
                                '_self'     =>'Страница в текущем окне',
                                '_top'      =>'Отменяет фреймы и загружает в текущее окно',
                            ),
                ),
                'content'           => array('type'=>'textarea', 'label'=>'Содержимое'),

//                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

}



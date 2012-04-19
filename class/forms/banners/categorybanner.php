<?php
/**
 * Форма категорий баннеров
 */
 
class Forms_Banners_CategoryBanner extends Form_Form
{
    function __construct()
    {
        parent::__construct(array(
            'name'      => 'CategoryBanner',
            'action'    => App::getInstance()->getRouter()->createServiceLink('banner', 'savecat'),
            'title'     => 'Настройка категорий баннеров',
            'fields'    => array(
                'id'                => array('type'=>'int', 'hidden'),
                'name'              => array(
                    'type' => 'text',
                    'class'=> 'elcatalog-meta',
                    'label'=> 'Название категории баннера',
                    'required',
                ),

//                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

}

<?php
/**
 * Форма категорий баннеров
 */

class Forms_Banners_CategoryBanner extends \Sfcms\Form\Form
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
                    'label'=> $this->t('Name'),
                    'required',
                ),

//                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 15.09.11
 * Time: 17:48
 * To change this template use File | Settings | File Templates.
 */
 
class Forms_Banners_CategoryBanner extends Form_Form
{
    function __construct()
    {
        parent::__construct(array(
            'name'      => 'CategoryBanner',
            'action'    => App::getInstance()->getRouter()->createServiceLink('banner', 'editcat'),
            'title'     => 'Настройка категорий баннеров',
            'fields'    => array(
                'id'                => array('type'=>'int', 'hidden'),
                'name'              => array('type'=>'text',  'class'=>'elcatalog-meta',  'label'=>'Название категории баннера',),

//                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }

}
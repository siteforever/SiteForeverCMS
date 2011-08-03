<?php
/**
 * Форма категории галлереи
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Forms_Gallery_Category extends form_Form
{
    function __construct()
    {
         // TODO Возможность задавать цвет добавляемых полей

        return parent::__construct(array(
            'name'      => 'gallery_category',
            'fields'    => array(
                'id'        => array('type'=>'int', 'hidden', 'value'=>'0'),
                'name'      => array('type'=>'text', 'label'=>'Наименование', 'required'),

                'middle_method' => array(
                    'type'  =>'select',
                    'label' =>'Метод создания средней картинки',
                    'value' =>'1',
                    'variants'=>array('1'=>'Добавление полей','2'=>'Обрезание лишнего',),
                ),
                'middle_width' => array(
                    'type'  => 'int',
                    'label' => 'Ширина средней картинки',
                    'value' => '200',
                ),
                'middle_height' => array(
                    'type'  => 'int',
                    'label' => 'Ширина средней картинки',
                    'value' => '200',
                ),
                'thumb_method' => array(
                    'type'  =>'select',
                    'label' =>'Метод создания миниатюры',
                    'value' =>'1',
                    'variants'=>array('1'=>'Добавление полей','2'=>'Обрезание лишнего',),
                ),
                'thumb_width' => array(
                    'type'  => 'int',
                    'label' => 'Ширина миниатюры',
                    'value' => '100',
                ),
                'thumb_height' => array(
                    'type'  => 'int',
                    'label' => 'Ширина миниатюры',
                    'value' => '100',
                ),
                'target'        => array(
                    'type'  => 'select',
                    'label' => 'Цель ссылок',
                    'variants'  => array(
                        '_gallery'  =>'Галерея',
                        '_blank'    =>'Изображение в новом окне',
                        '_self'     =>'Страница в текущем окне',
                        '_none'     =>'Без ссылки',
                    ),
                ),
                'perpage'       => array(
                    'type'  => 'int',
                    'label' => 'Изображений на страницу',
                    'value' => '20',
                ),
                'color'         => array(
                    'type'  => 'select',
                    'label' => 'Цвет полей',
                    'variants'=>array(
                        'ffffff'    => 'Белый',
                        '000000'    => 'Черный',
                        '-1'        => 'По первому пикселю',
                    ),
                ),
                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}

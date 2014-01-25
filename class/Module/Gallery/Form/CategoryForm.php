<?php
/**
 * Форма категории галлереи
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\Gallery\Form;

class CategoryForm extends \Sfcms\Form\Form
{
    function __construct()
    {
         // TODO Возможность задавать цвет добавляемых полей

        return parent::__construct(array(
            'name'      => 'gallery_category',
            'action'    => \Sfcms::html()->url('gallery/editcat'),
            'fields'    => array(
                'id'            => array( 'type'=> 'int', 'hidden', 'value'=> '0' ),
                'name'          => array( 'type'=> 'text', 'label'=> 'Наименование', 'required' ),

                'middle_method' => array(
                    'type'    => 'select',
                    'label'   => 'Метод создания средней картинки',
                    'value'   => '1',
                    'variants'=> array( '1'=> 'Добавление полей', '2'=> 'Обрезание лишнего', ),
                ),
                'middle_width'  => array(
                    'type'  => 'text',
                    'label' => 'Ширина средней картинки',
                    'value' => '200',
                ),
                'middle_height' => array(
                    'type'  => 'text',
                    'label' => 'Высота средней картинки',
                    'value' => '200',
                ),

                'thumb_method'  => array(
                    'type'    => 'select',
                    'label'   => 'Метод создания миниатюры',
                    'value'   => '1',
                    'variants'=> array( '1'=> 'Добавление полей', '2'=> 'Обрезание лишнего', ),
                ),
                'thumb_width'   => array(
                    'type'  => 'text',
                    'label' => 'Ширина миниатюры',
                    'value' => '100',
                ),
                'thumb_height'  => array(
                    'type'  => 'text',
                    'label' => 'Высота миниатюры',
                    'value' => '100',
                ),

                'target'        => array(
                    'type'      => 'select',
                    'label'     => 'Цель ссылок',
                    'variants'  => array(
                        '_gallery'  => 'Галерея',
                        '_blank'    => 'Изображение в новом окне',
                        '_self'     => 'Страница в текущем окне',
                        '_none'     => 'Без ссылки',
                    ),
                ),
                'perpage'       => array(
                    'type'     => 'select',
                    'label'    => 'Изображений на страницу',
                    'value'    => 20,
                    'variants' => array( 5=> '5', 10=> '10', 15=> '15', 20=> '20', 50=> '50' ),
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

//                'submit'        => array( 'type'=> 'submit', 'value'=> 'Сохранить' ),
            ),
        ));
    }
}

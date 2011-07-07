<?php
/**
 * Форма структуры сайта
 * @author keltanas
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class Forms_Page_Page extends Form_Form
{
    function __construct()
    {
        parent::__construct(array(
            'name'      => 'structure',
            'class'     => 'standart ajax',
            'fields'    => array(
                    'id'        => array('type'=>'hidden','label'=>'ID', 'value'=>'0',),
                    'parent'    => array('type'=>'hidden','label'=>'Parent', 'value'=>'0',),
                    'name'      => array('type'=>'text','label'=>'Наименование', 'required'),
                    'template'  => array('type'=>'text','label'=>'Шаблон', 'required'),
                    //'uri'       => array('type'=>'text','label'=>'Псевдоним', 'value='=>'', 'hidden'),
                    'alias'     => array('type'=>'text','label'=>'Адрес', 'required'),

                    'date'      => array('type'=>'date','label'=>'Дата создания', 'value'=>time(),),
                    'update'    => array('type'=>'date','label'=>'Дата обновления', 'value'=>time(),),

                    'pos'       => array('type'=>'int','label'=>'Порядок сортировки', 'value'=>'0',
                                         'readonly' ,'hidden',),

                    'controller'=> array(
                        'type'=>'select','label'=>'Контроллер', 'required',
                        'variants'  => array(),
                    ),
                    'link'      => array(
                        'type'=>'int', 'label'=>'Ссылка на раздел', 'value'=>'0',
                    ),
                    'action'    => array('type'=>'text','label'=>'Действие',
                                         'required', 'readonly', 'hidden'),

                    'sort'      => array('type'=>'text','label'=>'Сортировка',
                                         'required', 'hidden'),

                    //'sep1'       => array('type'=>'separator'),

                    'title'     => array('type'=>'text','label'=>'Заголовок'),
                    'keywords'  => array('type'=>'text','label'=>'Ключевые слова'),
                    'description'=> array('type'=>'text','label'=>'Описание'),

                    'notice'    => array('type'=>'textarea','label'=>'Вступление', 'value'=>'', 'hidden'),
                    'content'   => array('type'=>'textarea','label'=>'Текст',),

                    //'sep2'       => array('type'=>'separator'),

                    'thumb'     => array('type'=>'text','label'=>'Иконка'),
                    'image'     => array('type'=>'text','label'=>'Изображение'),


                    'author'    => array('type'=>'hidden','label'=>'Автор', 'value'=>'1'),

                    'hidden'    => array(
                            'type'      => 'radio',
                            'label'     => 'Скрытое',
                            'value'     => '0',
                            'variants'  => array('Нет', 'Да'),
                    ),
                    'protected' => array(
                            'type'      => 'radio',
                            'label'     => 'Защита страницы',
                            'value'     => USER_GUEST,
                            'variants'  => array(),
                    ),
                    'system'    => array(
                            'type'      => 'radio',
                            'label'     => 'Системный',
                            'value'     => '0',
                            'variants'  => array('Нет', 'Да'),
                    ),

                    //'sep3'       => array('type'=>'separator'),

                    'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),

            ),        ));
    }
}
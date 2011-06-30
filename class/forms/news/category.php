<?php
/**
 * Форма редактирования категории новости
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class forms_news_Category extends form_Form
{
    function __construct()
    {
        parent::__construct(array(
             'name'      => 'news_category',
             'fields'    => array(
                     'id'        => array('type'=>'int', 'value'=>'0', 'hidden'),
                     'name'      => array('type'=>'text', 'label'=>'Наименование', 'required',),
                     'description'   => array('type'=>'text', 'label'=>'Описание',),
                     'show_content'  => array(
                             'type'      => 'radio',
                             'label'     => 'Отображать контент',
                             'value'     => '1',
                             'variants'  => array(1=>'Да',0=>'Нет',),
                     ),
                     'show_list'     => array(
                             'type'      => 'radio',
                             'label'     => 'Отображать список',
                             'value'     => '1',
                             'variants'  => array(1=>'Да',0=>'Нет',),
                     ),
                     'type_list'     => array(
                             'type'      => 'select',
                             'label'     => 'Тип списка',
                             'value'     => '1',
                             'variants'  => array(
                                 1   => 'В виде ленты новостей',
                                 2   => 'В виде списка',
                             ),
                     ),
                     'per_page'     => array(
                             'type'      => 'select',
                             'label'     => 'Материалов на страницу',
                             'value'     => '1',
                             'variants'  => array(
                                  5   => '5',
                                 10   => '10',
                                 20   => '20',
                                 50   => '50',
                             ),
                     ),
                     'hidden'    => array(
                             'type'      => 'radio',
                             'label'     => 'Скрытое',
                             'value'     => '0',
                             'variants'  => array(1=>'Да',0=>'Нет',),
                     ),
                     'protected' => array(
                             'type'      => 'radio',
                             'label'     => 'Защита страницы',
                             'value'     => USER_GUEST,
                             'variants'  => Model::getModel('User')->getGroups(),
                     ),

                     'deleted'   => array('type'=>'int', 'value'=>'0', 'hidden'),

                     'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
             ),

        ));
    }
}

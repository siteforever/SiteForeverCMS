<?php
/**
 * Форма редактирования категории новости
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Form;

use Sfcms\Model;

class CategoryForm extends \Sfcms\Form\Form
{
    function __construct()
    {
        parent::__construct(array(
             'name'      => 'news_category',
            'class'      => 'form-horizontal',
             'action'    => \App::cms()->getRouter()->createServiceLink('news', 'catedit'),
             'fields'    => array(
                     'id'        => array('type'=>'int', 'value'=>'0', 'hidden'),
                     'name'      => array('type'=>'text', 'label'=>'Наименование', 'required',),
                     'description'   => array('type'=>'text', 'label'=>'Описание',),
                     'show_content'  => array(
                             'type'      => 'checkbox',
                             'label'     => 'Отображать контент',
                             'value'     => '1',
                     ),
                     'show_list'     => array(
                             'type'      => 'checkbox',
                             'label'     => 'Отображать список',
                             'value'     => '1',
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
                             'type'      => 'checkbox',
                             'label'     => 'Скрытое',
                             'value'     => '0',
                     ),
                     'protected' => array(
                             'type'      => 'select',
                             'label'     => 'Защита страницы',
                             'value'     => USER_GUEST,
                             'variants'  => \App::cms()->getDataManager()->getModel('User')->getGroups(),
                     ),

                     'deleted'   => array('type'=>'int', 'value'=>'0', 'hidden'),

                     'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
             ),

        ));
    }
}

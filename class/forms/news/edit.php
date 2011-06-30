<?php
/**
 * Форма редактирования новости
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class forms_news_Edit extends form_Form
{

    function __construct()
    {
        $app    = App::getInstance();

        $category   = Model::getModel('model_NewsCategory');
        $cats_data = $category->findAll();


        $cats   = array(0=>'Ничего не выбрано');
        foreach ( $cats_data as $_cd ) {
            $cats[$_cd['id']] = $_cd['name'];
        }

        parent::__construct(array(
            'name'      => 'news',
            'fields'    => array(
                'id'        => array('type'=>'int', 'value'=>'0', 'hidden',),
                'cat_id'    => array(
                    'type'      =>  'select',
                    'value'     =>  $app->getRequest()->get('cat'),
                    'variants'  =>  $cats,
                    'label'     =>  'Категория',
                    //'hidden',
                ),
                'author_id' => array('type'=>'text', 'value'=>$app->getAuth()->currentUser()->getId(), 'label'=>'','hidden',),
                'name'      => array('type'=>'text', 'value'=>'', 'label'=>'Название',),
                'notice'    => array('type'=>'textarea', 'value'=>'', 'label'=>'Вступление',),
                'text'      => array('type'=>'textarea', 'value'=>'', 'label'=>'Текст',),
                'date'      => array('type'=>'date', 'label'=>'Дата',),
                'title'     => array('type'=>'text', 'value'=>'', 'label'=>'Заголовок',),
                'keywords'  => array('type'=>'text', 'value'=>'', 'label'=>'Ключевые слова',),
                'description'=> array('type'=>'text', 'value'=>'','label'=>'Описание',),
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
                    'variants'  => Model::getModel('User')->getGroups(),
                ),

                'deleted'   => array('type'=>'int', 'value'=>'0', 'hidden'),

                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}

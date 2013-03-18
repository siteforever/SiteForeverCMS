<?php
/**
 * Форма редактирования новости
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

use Sfcms\Model;

class Forms_News_Edit extends \Sfcms\Form\Form
{

    function __construct()
    {
        $app    = App::getInstance();

        $category   = Model::getModel('NewsCategory');
        $cats_data = $category->findAll();

        $cats   = array(0=>'Ничего не выбрано');
        foreach ( $cats_data as $_cd ) {
            $cats[$_cd['id']] = $_cd['name'];
        }

        parent::__construct(array(
            'name'      => 'news',
            'action'    => App::getInstance()->getRouter()->createServiceLink('news','edit'),
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
                'main'      => array(
                    'type'=>'radio',
                    'label'=>'Показывать на главной',
                    'value'=>'0',
                    'variants' => array('0' => 'Нет', '1' => 'Да'),
                ),
                'priority'  => array(
                    'type'=>'radio',
                    'label'=>'Приоритет',
                    'value'=>'0',
                    'variants' => array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'),
                ),

                'notice'    => array('type'=>'textarea', 'value'=>'', 'label'=>'Вступление',),
                'text'      => array('type'=>'textarea', 'value'=>'', 'label'=>'Текст',),
                'date'      => array('type'=>'date', 'label'=>'Дата',),
                'image'     => array('type'=>'text', 'class'=>'image', 'label' => 'Изображение'),
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

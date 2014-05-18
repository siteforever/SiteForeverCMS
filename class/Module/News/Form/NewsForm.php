<?php
/**
 * Форма редактирования новости
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Form;

use Sfcms\Model;

/**
 * Class NewsForm
 *
 * @property $id
 * @property $cat_id
 * @property $author_id
 * ...
 */
class NewsForm extends \Sfcms\Form\Form
{
    public function __construct()
    {
        $app    = \App::cms();

        $category   = Model::getModel('NewsCategory');
        $cats_data = $category->findAll();

        $cats   = array(0=>'Ничего не выбрано');
        foreach ( $cats_data as $_cd ) {
            $cats[$_cd['id']] = $_cd['name'];
        }

        parent::__construct(array(
            'name'      => 'news',
            'action'    => \App::cms()->getRouter()->createServiceLink('news','edit'),
            'fields'    => array(
                'id'        => array('type'=>'int', 'value'=>null, 'hidden',),
                'cat_id'    => array(
                    'type'      =>  'select',
                    'value'     =>  '0',
                    'variants'  =>  $cats,
                    'label'     =>  'Категория',
                    //'hidden',
                ),
                'author_id' => array('type'=>'text', 'value'=>$app->getAuth()->getId(), 'label'=>'','hidden',),
                'name'      => array('type'=>'text', 'value'=>'', 'label'=>'Название', 'required',),
                'alias'     => array('type'=>'text', 'value'=>'', 'label'=>'Ссылка'),
                'main'      => array(
                    'type'=>'checkbox',
                    'label'=>'Показывать на главной',
                    'value'=>'0',
                    'variants' => array('0' => 'Нет', '1' => 'Да'),
                ),
                'priority'  => array(
                    'type'=>'select',
                    'label'=>'Приоритет',
                    'value'=>'0',
                    'variants' => range(0, 5),
                ),

                'notice'    => array('type'=>'textarea', 'value'=>'', 'label'=>'Вступление',),
                'text'      => array('type'=>'textarea', 'value'=>'', 'label'=>'Текст',),
                'date'      => array('type'=>'date', 'label'=>'Дата',),
                'image'     => array('type'=>'text', 'class'=>'image', 'label' => 'Изображение'),
                'title'     => array('type'=>'text', 'value'=>'', 'label'=>'Заголовок',),
                'keywords'  => array('type'=>'text', 'value'=>'', 'label'=>'Ключевые слова',),
                'description'=> array('type'=>'text', 'value'=>'','label'=>'Описание',),
                'hidden'    => array(
                    'type'      => 'checkbox',
                    'label'     => 'Скрытое',
                    'value'     => '0',
                ),
                'protected' => array(
                    'type'      => 'select',
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

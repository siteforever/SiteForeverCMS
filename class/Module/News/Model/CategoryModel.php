<?php
/**
 * Категории новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\News\Model;

use Module\News\Object\Category;
use Sfcms\Model;
use Forms_News_Category;

class CategoryModel extends Model
{
    /** @var Forms_News_Category */
    private $form = null;

    public function relation()
    {
        return array(
            'Page' => array( self::BELONGS, 'Page', 'link' ),
        );
    }

    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами новостей
     *
     * @param \Sfcms\Model\ModelEvent $event
     */
    public function pluginPageSaveStart( Model\ModelEvent $event )
    {
        $obj = $event->getObject();

        $category = null;
        if ( $obj->link ) {
            $category = $this->find( $obj->link );
        }
        if ( ! $category ) {
            $category = $this->createObject();
        }

        /** @var $category Category */
        $category->name         = $obj->name;

        if ( ! $category->id ) {
            $category->description  = '';
            $category->show_list    = 1;
            $category->type_list    = 1;
            $category->show_content = 0;
            $category->per_page     = 10;
        }

        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted ?: 0;

        $category->save();

        $obj->link = $category->id;
    }

    /**
     * @return Forms_News_Category
     */
    public function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_News_Category();
        }
        return $this->form;
    }

}

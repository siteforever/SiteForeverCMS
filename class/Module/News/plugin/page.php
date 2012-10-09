<?php
/**
 * Плугин связывает категории новостей со страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\News\Plugin;
use Sfcms\Model\Plugin;

class Page extends Plugin
{
    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами новостей
     *
     * @param \Data_Object_Page $obj
     */
    public function onSaveStart( \Data_Object_Page $obj )
    {
        $categoryModel = $obj->getModel('NewsCategory');

        $category = null;
        if ( $obj->link ) {
            $category = $categoryModel->find( $obj->link );
        }
        if ( ! $category ) {
            $category = $categoryModel->createObject();
        }

        /** @var $category \Data_Object_NewsCategory */
        $category->name         = $obj->name;
        $category->description  = '';

        $category->show_content = 0;
        $category->show_list    = 1;
        $category->type_list    = 1;
        $category->per_page     = 10;

        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted ?: 0;

//        \App::getInstance()->getLogger()->log($category->changed(),'newsPlugin');

        $category->save();

        $obj->link = $category->id;
    }
}

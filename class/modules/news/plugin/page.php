<?php
/**
 * Плугин связывает категории новостей со страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Modules\News\Plugin;

class Page extends \Sfcms\Model\Plugin
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

        if ( $obj->link ) {
            $category = $categoryModel->find( $obj->link );
        } else {
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
        $category->deleted      = $obj->deleted;

        $category->markDirty();
        $obj->link = $category->id;
    }
}

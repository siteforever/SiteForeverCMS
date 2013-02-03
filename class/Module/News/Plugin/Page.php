<?php
/**
 * Плугин связывает категории новостей со страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\News\Plugin;

use Module\News\Object\Category;
use Sfcms\Model\Plugin;
use Module\Page\Object\Page as PageObject;

class Page extends Plugin
{
    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами новостей
     *
     * @param PageObject $obj
     */
    public function onSaveStart( PageObject $obj )
    {
        $categoryModel = $obj->getModel('NewsCategory');

        $category = null;
        if ( $obj->link ) {
            $category = $categoryModel->find( $obj->link );
        }
        if ( ! $category ) {
            $category = $categoryModel->createObject();
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

//        \App::getInstance()->getLogger()->log($category->changed(),'newsPlugin');

        $category->save();

        $obj->link = $category->id;
    }
}

<?php
/**
 * Плугин связывает категории каталога со страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Modules\Catalog\Plugin;

class Page extends \Sfcms\Model\Plugin
{
    /**
     * Пересортировка
     *
     * Вызывается при пересортировке страниц.
     * Сюда передается объект страницы с новым параметром link.
     *
     * @param \Data_Object_Page $obj
     */
    public function resort( \Data_Object_Page $obj )
    {
        /** @var $catObj \Data_Object_Catalog */
        $catObj = \App::getInstance()->getModel('Catalog')->find( $obj->link );
        $catObj->pos = $obj->pos;
        $catObj->markDirty();
    }

    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами каталога
     *
     * @param \Data_Object_Page $obj
     */
    public function onSaveStart( \Data_Object_Page $obj )
    {
        $catalogModel = $obj->getModel('Catalog');
        $pageModel    = $obj->getModel('Page');

        if ( $obj->link ) {
            $category = $catalogModel->find( $obj->link );
        } else {
            $category = $catalogModel->createObject();
        }
        /** @var $category \Data_Object_Catalog */
        $category->name         = $obj->name;
        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted;

        $category->cat = 1;

        if ( $obj->parent ) {
            /** @var $parentPage \Data_Object_Page */
            $parentPage = $pageModel->find( $obj->parent );
            if ( $parentPage->controller == $obj->controller && $parentPage->link ) {
                $category->parent = $parentPage->link;
            } else {
                $category->parent = 0;
            }
        }

        $category->markDirty();
        $obj->link = $category->id;
    }
}

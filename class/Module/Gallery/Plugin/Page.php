<?php
/**
 * Плугин связывает категории галереи со страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Gallery\Plugin;

use Sfcms\Model\Plugin;

class Page extends Plugin
{
    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами галереи
     *
     * @param \Data_Object_Page $obj
     */
    public function onSaveStart( \Data_Object_Page $obj )
    {
        $categoryModel = $obj->getModel('GalleryCategory');

        if ( $obj->link ) {
            $category = $categoryModel->find( $obj->link );
        } else {
            $category = $categoryModel->createObject();
        }
        /** @var $category \Data_Object_Catalog */
        $category->name         = $obj->name;
        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted;

        $category->save();
        $obj->link = $category->id;
    }
}

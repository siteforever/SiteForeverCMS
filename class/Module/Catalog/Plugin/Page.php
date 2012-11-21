<?php
/**
 * Плугин связывает категории каталога со страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Catalog\Plugin;

use App;
use Sfcms\Model\Plugin;
use Data_Object_Page;
use Data_Object_Catalog;

class Page extends Plugin
{
    /**
     * Пересортировка
     *
     * Вызывается при пересортировке страниц.
     * Сюда передается объект страницы с новым параметром link.
     *
     * @param Data_Object_Page $obj
     */
    public function resort( Data_Object_Page $obj )
    {
        /** @var $catObj Data_Object_Catalog */
        $catObj = App::getInstance()->getModel('Catalog')->find( $obj->link );
        $catObj->pos = $obj->pos;
        $catObj->markDirty();
    }

    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами каталога
     *
     * @param Data_Object_Page $obj
     */
    public function onSaveStart( Data_Object_Page $obj )
    {
        $catalogModel = $obj->getModel('Catalog');
        $pageModel    = $obj->getModel('Page');

        /** @var $category Data_Object_Catalog */
        $category = null;
        if ( $obj->link ) {
            $category = $catalogModel->find( $obj->link );
        }
        if ( ! $category ) {
            $category = $catalogModel->createObject();
        }

        // Надо скрыть или показать все товары в данной категории, если изменился уровень видимости категории
        if ( $category->id
            && ( $category->hidden != $obj->hidden || $category->protected != $obj->protected || $category->deleted != $obj->deleted ) )
        {
            array_map(function( $product ) use ( $obj ) {
                /** @var $product Data_Object_Catalog */
                $product->hidden = $obj->hidden;
                $product->protected = $obj->protected;
                $product->deleted = $obj->deleted;
            },iterator_to_array($category->Goods));
        }

        /** @var $category Data_Object_Catalog */
        $category->name         = $obj->name;
        $category->pos          = $obj->pos;
        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted;

        $category->cat = 1;

        if ( $obj->parent && ! $category->parent ) {
            /** @var $parentPage Data_Object_Page */
            $parentPage = $pageModel->find( $obj->parent );
            if ( $parentPage->controller == $obj->controller && $parentPage->link ) {
                $category->parent = $parentPage->link;
            } else {
                $category->parent = 0;
            }
        }
        $category->save();
        $obj->link = $category->id;
    }
}

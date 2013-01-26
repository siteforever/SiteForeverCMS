<?php
/**
 * Плугин связывает категории каталога со страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Catalog\Plugin;

use App;
use Module\Catalog\Object\Catalog;
use Sfcms\Model\Plugin;
use Module\Page\Object\Page as PageObject;

class Page extends Plugin
{
    /**
     * Пересортировка
     *
     * Вызывается при пересортировке страниц.
     * Сюда передается объект страницы с новым параметром link.
     *
     * @param PageObject $obj
     */
    public function resort( PageObject $obj )
    {
        /** @var $catObj Catalog */
        $catObj = App::getInstance()->getModel('Catalog')->find( $obj->link );
        $catObj->pos = $obj->pos;
        $catObj->markDirty();
    }

    /**
     * Вызывается перед сохранением страницы
     *
     * Цель: создать связь страниц с объектами каталога
     *
     * @param PageObject $obj
     */
    public function onSaveStart( PageObject $obj )
    {
        $catalogModel = $obj->getModel('Catalog');
        $pageModel    = $obj->getModel('Page');

        /** @var $category Catalog */
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
                /** @var $product Catalog */
                $product->hidden = $obj->hidden;
                $product->protected = $obj->protected;
                $product->deleted = $obj->deleted;
            },iterator_to_array($category->Goods));
        }

        /** @var $category Catalog */
        $category->name         = $obj->name;
        $category->pos          = $obj->pos;
        $category->hidden       = $obj->hidden;
        $category->protected    = $obj->protected;
        $category->deleted      = $obj->deleted;

        $category->cat = 1;

        if ( $obj->parent && ! $category->parent ) {
            /** @var $parentPage PageObject */
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

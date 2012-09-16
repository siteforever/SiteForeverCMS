<?php
/**
 * Модуль Галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Modules\Gallery;

class Module extends \Sfcms\Module
{
    public static function relatedField()
    {
        return 'id';
    }

    public static function relatedModel()
    {
        return 'GalleryCategory';
    }


}

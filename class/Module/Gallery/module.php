<?php
/**
 * Модуль Галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Gallery;

use Sfcms\Module as SfModule;

class Module extends SfModule
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

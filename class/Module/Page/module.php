<?php
/**
 * Модуль страницы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Page;

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    public static function relatedField()
    {
        return 'id';
    }

}

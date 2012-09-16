<?php
/**
 * Модуль страницы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Modules\Page;

class Module extends \Sfcms\Module
{
    public static function relatedField()
    {
        return 'id';
    }

}

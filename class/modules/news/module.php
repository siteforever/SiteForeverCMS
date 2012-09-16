<?php
/**
 * Модуль новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Modules\News;

class Module extends \Sfcms\Module
{
    public static function relatedModel()
    {
        return 'NewsCategory';
    }
}

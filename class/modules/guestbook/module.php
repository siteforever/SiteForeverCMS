<?php
/**
 * Модуль гостевой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Modules\Guestbook;

class Module extends \Sfcms\Module
{
    public static function relatedField()
    {
        return 'id';
    }
}

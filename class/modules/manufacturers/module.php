<?php
/**
 * Модуль производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Modules\Manufacturers;

class Module extends \Sfcms\Module
{
    /**
     * Вернет поле, которое связывает страницу с ее модулем
     * @static
     * @return string
     */
    public static function relatedField()
    {
        return 'id';
    }
}

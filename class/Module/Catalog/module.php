<?php
/**
 * Модуль каталога
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Catalog;

use Sfcms\Module as SfModule;

class Module extends SfModule
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

    public static function relatedModel()
    {
        return 'Catalog';
    }

}

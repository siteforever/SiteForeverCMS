<?php
/**
 * Модуль обратной связи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Feedback;

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    /**
     * @inherit
     */
    public static function relatedField()
    {
        return 'id';
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return include_once __DIR__ . '/config.php';
    }

}

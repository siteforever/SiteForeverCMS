<?php
/**
 * Модуль баннеров
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Banner;

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return include_once __DIR__ . '/config.php';
    }

    public static function relatedModel()
    {
        return 'Page';
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Баннеры',
                'url'   => 'banner/admin',
            )
        );
    }
}

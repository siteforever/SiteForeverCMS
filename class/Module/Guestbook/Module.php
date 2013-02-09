<?php
/**
 * Модуль гостевой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Guestbook;

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    public static function relatedField()
    {
        return 'id';
    }

    public static function relatedModel()
    {
        return 'Page';
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return include_once __DIR__ . '/config.php';
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Гостевая',
                'url'   => 'guestbook/admin',
            )
        );
    }
}

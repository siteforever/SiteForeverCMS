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
                'name'  => 'Структура',
                'url'   => 'page/admin',
            )
        );
    }
}

<?php
/**
 * Модуль новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\News;

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    public static function relatedModel()
    {
        return 'NewsCategory';
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
                'name'  => 'Новости/статьи',
                'url'   => 'news/admin',
            )
        );
    }
}

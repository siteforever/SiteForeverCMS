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
                'name'  => 'Каталог',
                'sub'   => array(
                    array(
                        'name'  => 'Каталог',
                        'url'   => 'catalog/admin',
                    ),
                    array(
                        'name'  => t('Goods'),
                        'url'   => 'goods/admin'
                    ),
                    array(
                        'name'  => t('catalog','Product types'),
                        'url'   => 'prodtype/admin'
                    ),
                    array(
                        'name'  => t('material','Materials'),
                        'url'   => 'material/admin'
                    ),
                    array(
                        'name'  => t('Manufacturers'),
                        'url'   => 'manufacturers/admin'
                    ),
                )
            )
        );
    }

}

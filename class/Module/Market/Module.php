<?php
/**
 * Модуль производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Market;

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
                'name'  => 'Интернет магазин',
                'sub'   => array(
                    array(
                        'name'  => t('Payment'),
                        'url'   => 'payment/admin'
                    ),
                    array(
                        'name'  => t('delivery','Delivery'),
                        'url'   => 'delivery/admin'
                    ),
                    array(
                        'name'  => 'Заказы',
                        'url'   => 'order/admin',
                    ),
                ),
            ),
        );
    }
}

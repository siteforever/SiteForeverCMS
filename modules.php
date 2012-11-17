<?php
/**
 *  Конфигурация
 */
return array(
    array(
        'name'  => 'Структура',
        'url'   => 'page/admin',
    ),
    array(
        'name'  => 'Новости/статьи',
        'url'   => 'news/admin',
    ),
    array(
        'name'  => 'Баннеры',
        'url'   => 'banner/admin',
    ),
    array(
        'name'  => 'Галерея',
        'url'   => 'gallery/admin',
    ),
    array(
        'name'  => 'Гостевая',
        'url'   => 'guestbook/admin',
    ),
    array(
        'name'  => 'Пользователи',
        'url'   => 'users/admin',
    ),
    array(
        'name'  => 'Интернет магазин',
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
                'name'  => t('material','Materials'),
                'url'   => 'material/admin'
            ),
            array(
                'name'  => t('Manufacturers'),
                'url'   => 'manufacturers/admin'
            ),
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
    array(
        'name'=> 'Сервис',
        'sub' => array(
            array(
                'name'  => 'Менеджер файлов',
                'url'   => 'elfinder/finder',
                'class' => 'filemanager',
            ),
            array(
                'name'  => 'Архивация базы',
                'url'   => '/_runtime/sxd',
                'class' => 'dumper',
            ),
            array(
                'name'  => 'Поиск',
                'url'   => 'search/admin',
            ),
        )
    ),
    array(
        'name' => 'Система',
        'sub' => array(
            array(
                'name'  => 'Маршруты',
                'url'   => 'routes/admin',
            ),
            array(
                'name'  => 'Конфигурация системы',
                'url'   => 'system',
            ),
            array(
                'name'  => 'Настройка',
                'url'   => 'setting/admin',
            ),
            array(
                'name'  => 'Генератор',
                'url'   => 'generator',
            ),
        ),
    ),
    array(
        'name'  => 'Выход',
        'url'   => 'users/logout',
    ),
);

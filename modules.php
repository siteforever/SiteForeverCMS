<?php
/**
 *  Конфигурация
 */
return array(
    array(
        'name'      => 'Сайт',
        'url'       => '/',
        'norefact'  => true,
        'target'    => '_blank',
    ),
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
        'name'  => 'Каталог',
        'url'   => 'catalog/admin',
    ),
    array(
        'name'  => t('Manufacturers'),
        'url'   => 'manufacturers/admin'
    ),
    array(
        'name'  => t('Goods'),
        'url'   => 'goods/admin'
    ),
    array(
        'name'  => 'Галерея',
        'url'   => 'gallery/admin',
    ),
    array(
        'name'  => 'Пользователи',
        'url'   => 'users/admin',
    ),
    array(
        'name'  => 'Заказы',
        'url'   => 'order/admin',
    ),
    array(
        'name'  => 'Менеджер файлов',
        'url'   => 'filemanager/admin',
        'class' => 'filemanager',
    ),
    array(
        'name'  => 'Архивация базы',
        'url'   => '/_runtime/sxd',
        'class' => 'dumper',
    ),
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
        'url'   => 'settings/admin',
    ),
    array(
        'name'  => 'Генератор',
        'url'   => 'generator',
    ),
    array(
        'name'  => 'Выход',
        'url'   => 'users/logout',
    ),
);

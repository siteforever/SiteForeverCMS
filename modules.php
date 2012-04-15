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
        'url'   => 'admin',
        'sub'   => array(
            array(
                'name'  => 'Добавить страницу',
                'url'   => 'page/add/add=0',
                'icon'  => 'page_add',
            ),
        ),
    ),
    array(
        'name'  => 'Материалы',
        'url'   => 'admin/news',
    ),
    array(
        'name'  => 'Каталог',
        'url'   => 'admin/catalog',
        'sub'   => array(
            array(
                'name'  => 'Добавить раздел',
                'url'   => 'catalog/category/add=0',
                'icon'  => 'folder_add',
            ),
        ),
    ),
    array(
        'name'  => 'Галерея',
        'url'   => 'admin/gallery',
    ),
    array(
        'name'  => 'Пользователи',
        'url'   => 'users/admin',
        'sub'   => array(
            array(
                'name'  => 'Добавить пользователя',
                'url'   => 'users/admin/add/1',
                'icon'  => 'user_add',
            ),
        ),
    ),
    array(
        'name'  => 'Заказы',
        'url'   => 'admin/order',
    ),
    array(
        'name'  => 'Менеджер файлов',
        'url'   => 'admin/filemanager',
        'class' => 'filemanager',
    ),
    array(
        'name'  => 'Архивация базы',
        'url'   => '/_runtime/sxd',
        'class' => 'dumper',
    ),
    array(
        'name'  => 'Маршруты',
        'url'   => 'admin/routes',
    ),
    array(
        'name'  => 'Конфигурация системы',
        'url'   => 'system',
    ),
    array(
        'name'  => 'Настройка',
        'url'   => 'admin/settings',
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

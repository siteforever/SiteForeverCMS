<?php
/**
 *  Конфигурация
 */
return array(
    array(
        'name'  => 'Сайт',
        'url'  => '/',
        'norefact'  => true,
        'target'    => '_blank',
    ),
    array(
        'name'  => 'Структура',
        'url'   => 'admin',
        'sub'   => array(
            array(
                'name'  => 'Добавить страницу',
                'url'   => 'admin/add/add=0',
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
                'url'   => 'admin/catalog/add=0/type=1',
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
        'url'   => 'admin/users',
        'sub'   => array(
            array(
                'name'  => 'Добавить пользователя',
                'url'   => 'admin/users/add=1',
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
        'url'   => '/misc/dumper/dumper.php',
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
        'name'  => 'Выход',
        'url'   => 'users/logout',
    ),
);

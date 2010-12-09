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
        'name'  => 'Галлерея',
        'url'   => 'admin/gallery',
    ),
    array(
        'name'  => 'Пользователи',
        'url'   => 'admin/users',
        'sub'   => array(
                array(
                    'name'  => 'Добавить пользователя',
                    'url'   => 'admin/users/add',
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
    /*array(
        'name'  => 'Настройки',
        'url'   => 'admin/settings',
    ),*/
    array(
        'name'  => 'Маршруты',
        'url'   => 'admin/routes',
    ),
    array(
        'name'  => 'Выход',
        'url'   => 'users/logout',
    ),
    /*array(
        'name'  => 'Конфигурация системы',
        'url'   => 'system',
    ),*/
);

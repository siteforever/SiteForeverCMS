<?php
/**
 * Конфиг для системы
 */
return array(

    // отладка
    'debug' => array(
        'profiler'   => true,
    ),

    'cache' => false,

//    'logger'    => 'firephp',
//    'logger'    => 'html',

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],
    'admin'     => 'admin@ermin.ru',

    'url'       => array(
        'rewrite'   => true,
    ),

    'language'  => 'ru',

    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'siteforever',
        'debug'     => true,
        'migration' => true,
    ),

    // тема
    'template' => require 'template.php',

    // настройки пользователей
    'users' => array(
        'userdir' => DIRECTORY_SEPARATOR.'files',
    ),

    'catalog' => array(
        'gallery_dir' => DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'catalog'.DIRECTORY_SEPARATOR.'gallery',
        'gallery_max_file_size' => 1000000,
        'gallery_thumb_prefix'  => 'thumb_',
        'gallery_thumb_h'   => 100,
        'gallery_thumb_w'   => 100,
        'gallery_thumb_prefix'  => 'middle_',
        'gallery_thumb_h'   => 200,
        'gallery_thumb_w'   => 200,
        'gallery_thumb_method' => 1,
            // 1 - добавление полей
            // 2 - обрезание лишнего
        // сортировка товаров
        'order_list'    => array(
            ''      => 'Без сортировки',
            'name'  => 'По наименованию',
            'price1'=> 'По цене',
            'articul'=>'По артикулу',
        ),
    ),
);
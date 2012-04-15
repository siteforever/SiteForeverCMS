<?php
/**
 * Конфиг для системы
 */
return array(

    // отладка
    'debug' => array(
        'profiler'   => true,
    ),

//    'logger'    => 'firephp',
//    'logger'    => 'html',

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],
    'admin'     => 'admin@ermin.ru',

    'url'       => array(
        'rewrite'   => true,
    ),

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
    'template' => array(
        'theme'     => 'basic',
        // драйвер шаблонизатора
        // это класс, поддерживающий интерфейс TPL_Driver
        'driver'    => 'TPL_Smarty',
        'widgets'   => SF_PATH.DIRECTORY_SEPARATOR.'widgets',
        'ext'       => 'tpl', // расширение шаблонов
        'admin'     => SF_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'system', // каталог шаблонов админки
        '404'       => 'error404', // шаблон страницы 404
    ),

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
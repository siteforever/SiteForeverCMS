<?php
return array(
    // отладка
    'debug' => array(
        'profiler'   => false,
    ),

    'logger'    => 'auto',
    //    'logger'    => 'html',
    //    'logger'    => 'file',

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],
    'admin'     => 'admin@ermin.ru',

    'url'       => array(
        'rewrite'   => true,
    ),

    'cache' => array(
        'type' => 'apc',
        'livecycle' => 600,
    ),

    //    'editor' => 'tinymce',
    'editor' => 'ckeditor',
    //    'editor' => 'elrte',

    'language'  => 'ru',

    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'siteforever',
        'debug'     => false,
        'migration' => false,
    ),

    // тема
    'template' => array(
        'theme'     => 'basic',
        // драйвер шаблонизатора
        // это класс, поддерживающий интерфейс TPL_Driver
        'driver'    => '\\Sfcms\\Tpl\\Smarty',
        'widgets'   => SF_PATH.DIRECTORY_SEPARATOR.'widgets',
        'ext'       => 'tpl', // расширение шаблонов
        'admin'     => SF_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'system', // каталог шаблонов админки
        '404'       => 'error404', // шаблон страницы 404
    ),

    'modules' => require_once __DIR__ . '/modules.php',

);

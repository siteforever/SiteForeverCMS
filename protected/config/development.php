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

    'logger'    => 'auto',
//    'logger'    => 'html',
//    'logger'    => 'file',

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],
    'admin'     => 'admin@ermin.ru',

    'url'       => array(
        'rewrite'   => true,
    ),

    'editor' => 'tinymce',
//    'editor' => 'elrte',

    'language'  => 'ru',

    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'siteforever',
        'debug'     => true,
        'migration' => true,
        //'autoGenerateMeta' => true,
    ),

    // тема
    'template' => array(
        'theme'     => 'basic',
        // драйвер шаблонизатора
        // это класс, поддерживающий интерфейс TPL_Driver
        'driver'    => '\\Sfcms\\Tpl\\Smarty',
        'version'   => '3.1.11',
        'widgets'   => SF_PATH.DIRECTORY_SEPARATOR.'widgets',
        'ext'       => 'tpl', // расширение шаблонов
        'admin'     => SF_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'system', // каталог шаблонов админки
        '404'       => 'error404', // шаблон страницы 404
    ),

    // настройки пользователей
    'users' => array(
        'userdir' => DIRECTORY_SEPARATOR.'files',
    ),
);
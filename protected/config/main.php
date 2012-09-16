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
        'autoGenerateMeta' => true,
    ),

    // тема
    'template' => require 'template.php',

    // настройки пользователей
    'users' => array(
        'userdir' => DIRECTORY_SEPARATOR.'files',
    ),
);
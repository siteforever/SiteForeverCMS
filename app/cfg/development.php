<?php
/**
 * Конфиг для системы
 */
$base = include_once('base.php');
return array(
    'cache' => array(
        'class' => 'Sfcms\Cache\CacheBlank',
        'livecycle' => 0,
    ),

    'logger'    => 'auto',
//    'logger'    => 'html',
//    'logger'    => 'file',

    'sitename'  => 'SiteForeverCMS',
    'admin'     => 'admin@ermin.ru',

    // база данных
    'db' => array(
        'dsn'       => 'mysql:host=localhost;dbname=siteforever',
        'login'     => 'root',
        'password'  => '',
        'options'   => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ),
        'debug'     => true,
        'migration' => true,
    ),
    'template' => array(
        'caching'   => true,
//        'cache' => array(
//            'livetime' => 3600,
//        ),
        'theme'     => 'basic',
    ),
) + $base;

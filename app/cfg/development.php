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
        'login'     => 'root',
        'password'  => '',
        'host'      => 'localhost',
        'database'  => 'siteforever',
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

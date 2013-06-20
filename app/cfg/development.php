<?php
/**
 * Конфиг для системы
 */
return array_merge(include_once 'base.php', array(
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
        'caching'   => false,
//        'cache' => array(
//            'livetime' => 3600,
//        ),
        'theme'     => 'basic',
    ),
));

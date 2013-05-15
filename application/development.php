<?php
/**
 * Конфиг для системы
 */
return array_merge(include_once 'base.php', array(
    // отладка
    'debug' => array(
        'profiler'   => true,
    ),

//    'cache' => false,

    'logger'    => 'auto',
//    'logger'    => 'html',
//    'logger'    => 'file',

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],
    'admin'     => 'admin@ermin.ru',


    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'siteforever',
        'debug'     => true,
        'migration' => true,
    ),
));

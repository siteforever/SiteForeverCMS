<?php
/**
 * Конфиг для системы
 */

return array(

    // отладка
    'debug' => array(
        'profile'   => false,
    ),

    'cache' => false,

    'logger'    => 'plain',

    'language'  => 'ru',


    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'example.test',
    'admin'     => 'admin@ermin.ru',

    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
//        'database'  => 'sftest',
        'database'  => 'siteforever',
        'migration' => false,
    ),

    // тема
    'template' => require 'template.php',

    // настройки пользователей
    'users' => array(
        'userdir' => DIRECTORY_SEPARATOR.'files',
    ),
);
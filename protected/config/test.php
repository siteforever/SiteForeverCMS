<?php
/**
 * Конфиг для системы
 */

return array(

    // отладка
    'debug' => array(
        'profile'   => true,
    ),

    'cache' => false,

    'logger'    => 'plain',

    'language'  => 'en',


    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'example.test',
    'admin'     => 'admin@ermin.ru',

    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'sftest',
        'migration' => false,
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
);
<?php
/**
 * Конфиг для системы
 */


// отладка загрузки классов
if ( ! defined('DEBUG_AUTOLOAD') ) {
    define('DEBUG_AUTOLOAD', 0);
}


return array(

    // отладка
    'debug' => true,

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'example.test',
    'admin'     => 'admin@ermin.ru',

    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'siteforever',
    ),
    // тема
    'template' => array(
        'theme'     => 'basic',
        // драйвер шаблонизатора
        // это класс, поддерживающий интерфейс TPL_Driver
        'driver'    => 'TPL_Smarty',
        'widgets'   => SF_PATH.DS.'widgets',
        'ext'       => 'tpl', // расширение шаблонов
        'admin'     => SF_PATH.DS.'themes'.DS.'system', // каталог шаблонов админки
        '404'       => 'error404', // шаблон страницы 404
    ),

    // настройки пользователей
    'users' => array(
        'groups' => array(
            USER_GUEST  => 'Гость',
            USER_USER   => 'Пользователь',
            USER_WHOLE  => 'Оптовый покупатель',
            USER_ADMIN  => 'Админ',
        ),
        'userdir' => DS.'files',
    ),
);
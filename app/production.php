<?php
/**
 * Production config sample
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
return array_merge(include_once 'base.php', array(
    // отладка
    'debug' => array(
        'profiler'   => false,
    ),
    'logger'    => 'blank',
    'sitename'  => 'Some site',
    'admin'     => 'admin@example.com',
    // база данных
    'db' => array(
        'login'     => 'root',
        'password'  => '',
        'host'      => 'localhost',
        'database'  => 'siteforever',
    ),
    // настройки пользователей
    'users' => array(
        'userdir' => DIRECTORY_SEPARATOR.'files',
    ),
));

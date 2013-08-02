<?php
/**
 * Production config sample
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
$base = include_once('base.php');
return array(
    // отладка
    'debug' => array(
        'profiler'   => false,
    ),
    'logger'    => 'blank',
    'sitename'  => 'Some site',
    'admin'     => 'admin@example.com',
    // база данных
    'db' => array(
        'dsn'       => 'mysql:host=localhost;dbname=siteforever',
        'login'     => 'root',
        'password'  => '',
        'options'   => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ),
    ),
    // настройки пользователей
    'users' => array(
        'userdir' => DIRECTORY_SEPARATOR.'files',
    ),
) + $base;

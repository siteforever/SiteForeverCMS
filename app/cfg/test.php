<?php
/**
 * Конфиг для тестов
 */
$base = include_once('base.php');
return array(
    'cache' => array(
        'class' => 'Sfcms\Cache\CacheBlank',
        'livecycle' => 0,
    ),
    'logger'    => 'file',
    'language'  => 'ru',
    'siteurl'   => 'localhost',

    // база данных
    'db' => array(
        'login'     => 'root',
        'password'  => '',
        'dsn'       => 'mysql:host=localhost;dbname=siteforever_test',
        'debug'     => true,
        'migration' => true,
        'options'   => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ),
    ),
    'pager_template' => 'pager',
    'mailer_transport' => 'null',
    'session_storage' => '@session.storage.mock',
) + $base;

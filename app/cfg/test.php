<?php
/**
 * Конфиг для тестов
 */
$base = include_once('base.php');
return array(
    'language'  => 'ru',
    'siteurl'   => 'localhost',

    // база данных
    'db' => array(
        'login'     => 'root',
        'password'  => null,
        'dsn'       => 'mysql:host=localhost;dbname=siteforever_test',
        'debug'     => true,
        'migration' => true,
        'options'   => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ),
    ),
    'mailer_transport' => 'null',
    'session_storage' => '@session.storage.mock',
) + $base;

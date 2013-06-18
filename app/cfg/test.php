<?php
/**
 * Конфиг для тестов
 */

return array_merge(require_once 'base.php', array(
    'cache' => false,
    'logger'    => 'file',
    'language'  => 'ru',
    'siteurl'   => 'localhost',

    // база данных
    'db' => array(
        'login'     => 'root',
        'password'  => '',
        'host'      => 'localhost',
        'database'  => 'siteforever_test',
        'debug'     => true,
        'migration' => true,
    ),

    'mailer' => array(
        'transport' => 'null',
    ),

));

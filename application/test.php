<?php
/**
 * Конфиг для системы
 */

return array_merge(require_once 'base.php', array(
    // отладка
    'debug' => array(
        'profiler'   => true,
    ),
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

));

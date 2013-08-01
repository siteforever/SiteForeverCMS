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
        'host'      => 'localhost',
        'database'  => 'siteforever_test',
        'debug'     => true,
        'migration' => true,
    ),
    'pager_template' => 'pager',
    'mailer_transport' => 'null',
) + $base;

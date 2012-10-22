<?php
/**
 * Production config sample
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
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
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'siteforever',
        'debug'     => false,
        'migration' => false,
        'autoGenerateMeta' => false,
    ),
    'template' => array(
        'theme'     => 'somesite',
    ),
);
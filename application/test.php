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
    'siteurl'   => 'example.test',
));
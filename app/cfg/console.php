<?php
/**
 * Конфиг для консоли
 */

return array_merge(require_once 'base.php', array(
    'cache' => false,
    'language'  => 'ru',
    'siteurl'   => 'localhost',
    'mailer' => array(
        'transport' => 'null',
    ),
));

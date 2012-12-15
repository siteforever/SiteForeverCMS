<?php
/**
 * Конфиг
 * @author: keltanas
 * @link http://siteforever.ru
 */

return array(
    'controllers' => array(
        'News'  => array(),
        'Rss'   => array(),
    ),
    'models' => array(
        'News'         => 'Module\\News\\Model\\NewsModel',
        'NewsCategory' => 'Module\\News\\Model\\CategoryModel',
    ),
);
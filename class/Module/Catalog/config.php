<?php
/**
 * Конфиг модуля
 * @author: keltanas
 * @link http://siteforever.ru
 */

return array(
    'controllers' => array(
        'Catalog'   => array(),
        'Cataloggallery' => array( 'class' => 'Controller\Gallery', ),
        'Goods'     => array(),
    ),
    'models' => array(
        'Catalog'        => 'Model_Catalog',
        'CatalogGallery' => 'Model_CatalogGallery',
    ),
);
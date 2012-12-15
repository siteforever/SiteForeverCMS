<?php
/**
 * Конфиг модуля
 * @author: keltanas
 * @link http://siteforever.ru
 */

return array(
    'controllers' => array(
        'Catalog'        => array(),
        'Cataloggallery' => array( 'class' => 'Controller\Gallery', ),
        'Goods'          => array(),
        'Prodtype'       => array(),
    ),
    'models' => array(
        'Catalog'         => 'Model_Catalog',
        'CatalogGallery'  => 'Model_CatalogGallery',

        'ProductType'     => 'Model_ProductType',
        'ProductProperty' => 'Model_ProductProperty',
        'ProductField'    => 'Model_ProductField',
    ),
);
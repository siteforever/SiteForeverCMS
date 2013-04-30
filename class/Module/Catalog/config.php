<?php
/**
 * Конфиг модуля
 * @author: keltanas
 * @link http://siteforever.ru
 */

return array(
    'controllers' => array(
        'Goods'          => array(),
        'Prodtype'       => array(),
        'Catalog'        => array(),
        'Cataloggallery' => array( 'class' => 'Controller\Gallery', ),
    ),
    'models' => array(
        'Catalog'         => 'Module\Catalog\Model\CatalogModel',
        'CatalogGallery'  => 'Module\Catalog\Model\GalleryModel',

        'ProductField'    => 'Module\Catalog\Model\FieldModel',
        'ProductProperty' => 'Module\Catalog\Model\PropertyModel',
        'ProductType'     => 'Module\Catalog\Model\TypeModel',
    ),
);
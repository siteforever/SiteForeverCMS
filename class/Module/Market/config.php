<?php
/**
 * Конфиг
 * @author: keltanas
 * @link http://siteforever.ru
 */

return array(
    'controllers' => array(
        'Basket'        => array(),
        'Delivery'      => array(),
        'Manufacturers' => array( 'class' => 'Controller\Manufacturer', ),
        'Material'      => array(),
        'Order'         => array(),
        'OrderAdmin'    => array(),
        'Orderpdf'      => array(),
        'Xmlprice'      => array(),
        'Payment'       => array(),
        'Producttype'   => array(),
        'Robokassa'     => array(),
    ),
    'models' => array(
        'Delivery'         => 'Model_Delivery',
        'Manufacturers'    => 'Model_Manufacturers',
        'Material'         => 'Model_Material',
        'Order'            => 'Model_Order',
        'OrderPosition'    => 'Model_OrderPosition',
        'OrderStatus'      => 'Model_OrderStatus',
        'Payment'          => 'Model_Payment',
        'Product_Field'    => 'Model_Product_Field',
        'Product_Property' => 'Model_Product_Property',
        'Product_Type'     => 'Model_Product_Type',
    ),
);
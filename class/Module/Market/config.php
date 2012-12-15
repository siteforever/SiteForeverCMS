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
        'Delivery'         => 'Module\\Market\\Model\\DeliveryModel',
        'Manufacturers'    => 'Module\\Market\\Model\\ManufacturerModel',
        'Material'         => 'Module\\Market\\Model\\MaterialModel',
        'Metro'            => 'Module\\Market\\Model\\MetroModel',
        'Order'            => 'Module\\Market\\Model\\OrderModel',
        'OrderPosition'    => 'Module\\Market\\Model\\OrderPositionModel',
        'OrderStatus'      => 'Module\\Market\\Model\\OrderStatusModel',
        'Payment'          => 'Module\\Market\\Model\\PaymentModel',
    ),
);
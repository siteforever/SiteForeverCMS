<?php
/**
 * Модель для позиции заказа
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
class Model_OrderPosition extends Sfcms_Model
{
    public function relation()
    {
        return array(
            'order' => array( self::BELONGS, 'Order', 'ord_id' ),
            'Product' => array( self::BELONGS, 'Catalog', 'product_id' ),
        );
    }
}

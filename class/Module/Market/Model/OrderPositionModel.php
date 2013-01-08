<?php
/**
 * Модель для позиции заказа
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Module\Market\Model;

use Sfcms\Model;

class OrderPositionModel extends Model
{
    public function relation()
    {
        return array(
            'order' => array( self::BELONGS, 'Order', 'ord_id' ),
            'Product' => array( self::BELONGS, 'Catalog', 'product_id' ),
        );
    }

    public function tableClass()
    {
        return 'Data_Table_OrderPosition';
    }

    public function objectClass()
    {
        return 'Data_Object_OrderPosition';
    }

}

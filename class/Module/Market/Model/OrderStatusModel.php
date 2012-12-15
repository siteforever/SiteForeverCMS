<?php
/**
 * Модель статуса заказа
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Module\Market\Model;

use Sfcms_Model;

class OrderStatusModel extends Sfcms_Model
{

    public function tableClass()
    {
        return 'Data_Table_OrderStatus';
    }

    public function objectClass()
    {
        return 'Data_Object_OrderStatus';
    }

}

<?php
/**
 * Модель Delivery
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms\Model;

class DeliveryModel extends Model
{

    public function tableClass()
    {
        return 'Data_Table_Delivery';
    }

    public function objectClass()
    {
        return 'Data_Object_Delivery';
    }

}
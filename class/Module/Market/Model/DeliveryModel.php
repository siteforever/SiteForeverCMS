<?php
/**
 * Модель Delivery
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms_Model;

class DeliveryModel extends Sfcms_Model
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
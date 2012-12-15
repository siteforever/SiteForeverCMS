<?php
/**
 * Модель Payment
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms_Model;

class PaymentModel extends Sfcms_Model
{

    public function tableClass()
    {
        return 'Data_Table_Payment';
    }

    public function objectClass()
    {
        return 'Data_Object_Payment';
    }

}
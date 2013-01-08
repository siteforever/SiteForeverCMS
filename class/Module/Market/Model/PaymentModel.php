<?php
/**
 * Модель Payment
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms\Model;

class PaymentModel extends Model
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
<?php
/**
 * Модель Product_Field
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Catalog\Model;

use Sfcms_Model;

class FieldModel extends Sfcms_Model
{
    public function tableClass()
    {
        return 'Data_Table_ProductField';
    }

    public function objectClass()
    {
        return 'Data_Object_ProductField';
    }


}
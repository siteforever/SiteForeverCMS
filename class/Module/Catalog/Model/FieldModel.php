<?php
/**
 * Модель Product_Field
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Catalog\Model;

use Sfcms\Model;

class FieldModel extends Model
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
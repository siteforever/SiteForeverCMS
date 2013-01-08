<?php
/**
 * Модель Product_Property
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Catalog\Model;

use Sfcms\Model;

/**
 * @property $product_id int
 * @property $product_field_id int
 * @property $value_string varchar
 * @property $value_text blob
 * @property $value_int int
 * @property $value_datetime datetime
 */
class PropertyModel extends Model
{
    public function relation()
    {
        return array(
            'Field' => array(self::BELONGS, 'ProductField', 'product_field_id', 'order' => 'pos'),
        );
    }

    public function tableClass()
    {
        return 'Data_Table_ProductProperty';
    }

    public function objectClass()
    {
        return 'Data_Object_ProductProperty';
    }

}
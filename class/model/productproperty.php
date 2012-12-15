<?php
/**
 * Модель Product_Property
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property $product_id int
 * @property $product_field_id int
 * @property $value_string varchar
 * @property $value_text blob
 * @property $value_int int
 * @property $value_datetime datetime
 */
class Model_ProductProperty extends Sfcms_Model
{
    public function relation()
    {
        return array(
            'Field' => array(self::BELONGS, 'ProductField', 'product_field_id', 'order' => 'pos'),
        );
    }
}
<?php
/**
 * Domain object Product_Property
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */
/**
 * @property int product_id
 * @property int product_field_id
 * @property string value_string
 * @property string value_text
 * @property int value_int
 * @property string value_datetime
 */
class Data_Object_ProductProperty extends Data_Object
{
    public function getName()
    {
        $field = $this->get('Field');
        return $field->name;
    }

    public function getValue()
    {
        $field = $this->get('Field');
        return $this->data['value_'.$field->type];
    }

    public function getUnit()
    {
        $field = $this->get('Field');
        return $field->unit;
    }
}

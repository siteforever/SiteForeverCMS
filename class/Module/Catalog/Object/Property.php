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
namespace Module\Catalog\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Property extends Object
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

    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int( 'product_id', 11, false, null, false ),
            new Field\Int( 'product_field_id', 11, false, null, false ),
            new Field\Varchar( 'value_string', 255, true, null, false ),
            new Field\Blob( 'value_text', 11, true, null, false ),
            new Field\Int( 'value_int', 11, true, null, false ),
            new Field\Datetime( 'value_datetime', 11, true, null, false ),
            new Field\Int( 'pos', 11, false, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'product_property';
    }

    /**
     * @return array|string
     */
    public static function pk()
    {
        return array('product_id','product_field_id');
    }
}

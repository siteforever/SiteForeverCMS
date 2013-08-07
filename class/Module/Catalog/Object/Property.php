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
use Sfcms\Data\Field as TField;

/**
 * Class Property
 * @package Module\Catalog\Object
 *
 * @property $product_id
 * @property $product_field_id
 * @property $value_string
 * @property $value_text
 * @property $value_int
 * @property $value_datetime
 * @property $pos
 *
 * @property $name
 * @property $value
 * @property $unit
 */
class Property extends Object
{
    public function getName()
    {
        $field = $this->get('Field');
        return $field ? $field->name : null;
    }

    public function getValue()
    {
        $field = $this->get('Field');
        return $field ? $this->data['value_'.$field->type] : null;
    }

    public function getUnit()
    {
        $field = $this->get('Field');
        return $field ? $field->unit : '';
    }

    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new TField\Int( 'product_id', 11, false, null, false ),
            new TField\Int( 'product_field_id', 11, false, null, false ),
            new TField\Varchar( 'value_string', 255, true, null, false ),
            new TField\Blob( 'value_text', 11, true, null, false ),
            new TField\Int( 'value_int', 11, true, null, false ),
            new TField\Datetime( 'value_datetime', 11, true, null, false ),
            new TField\Int( 'pos', 11, false, 0 ),
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

    public static function keys()
    {
        return array(
            'product_id' => 'product_id',
        );
    }


}

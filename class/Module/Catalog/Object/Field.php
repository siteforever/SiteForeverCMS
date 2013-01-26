<?php
/**
 * Domain object Product_Field
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */
/**
 * @property int id
 * @property int product_type_id
 * @property string type
 * @property string name
 * @property string unit
 */
namespace Module\Catalog\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field as TField;

class Field extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doGetFields()
    {
        return array(
            new TField\Int( 'id', 11, false, null, true ),
            new TField\Int( 'product_type_id', 11, false, 0 ),
            new TField\Varchar( 'type', 250, false, '' ),
            new TField\Varchar( 'name', 250, false, '' ),
            new TField\Varchar( 'unit', 250, false, '' ),
            new TField\Int( 'pos', 11, false, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function getTable()
    {
        return 'product_field';
    }
}

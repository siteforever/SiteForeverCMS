<?php
/**
 * Domain object Metro
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property int id
 * @property string name
 * @property int city_id
 * @property float lat
 * @property float lng
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Metro extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField( 'id', 10, false, null, true ),
            new Field\VarcharField( 'name', 50, true, null, false ),
            new Field\IntField( 'city_id', 10, true, null, false ),
            new Field\DecimalField( 'lat', '10,6', true, null, false ),
            new Field\DecimalField( 'lng', '10,6', true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'metro';
    }
}

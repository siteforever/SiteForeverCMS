<?php
/**
 * Domain object Manufacturer
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property int id
 * @property string name
 * @property string phone
 * @property string email
 * @property string address
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Manufacturer extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField( 'id', 11, false, null, true ),
            new Field\VarcharField( 'name', 250, true, null, false ),
            new Field\VarcharField( 'phone', 250, true, null, false ),
            new Field\VarcharField( 'email', 250, true, null, false ),
            new Field\VarcharField( 'site', 250, true, null, false ),
            new Field\TextField( 'address', 11, true, null, false ),
            new Field\VarcharField( 'image', 250, true, null, false ),
            new Field\TextField( 'description', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'manufacturers';
    }
}

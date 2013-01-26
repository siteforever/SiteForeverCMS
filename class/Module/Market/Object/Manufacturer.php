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
    protected static function doGetFields()
    {
        return array(
            new Field\Int( 'id', 11, false, null, true ),
            new Field\Varchar( 'name', 250, true, null, false ),
            new Field\Varchar( 'phone', 250, true, null, false ),
            new Field\Varchar( 'email', 250, true, null, false ),
            new Field\Varchar( 'site', 250, true, null, false ),
            new Field\Text( 'address', 11, true, null, false ),
            new Field\Varchar( 'image', 250, true, null, false ),
            new Field\Text( 'description', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function getTable()
    {
        return 'manufacturers';
    }
}

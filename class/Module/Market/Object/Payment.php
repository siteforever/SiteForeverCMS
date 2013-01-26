<?php
/**
 * Domain object Payment
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property int id
 * @property string name
 * @property string desc
 * @property string module
 * @property int active
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Payment extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doGetFields()
    {
        return array(
            new Field\Int( 'id', 11, false, null, true ),
            new Field\Varchar( 'name', 255, false, null, false ),
            new Field\Text( 'desc', 11, false, null, false ),
            new Field\Varchar( 'module', 255, false, null, false ),
            new Field\Tinyint( 'active', 1, false, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function getTable()
    {
        return 'payment';
    }
}

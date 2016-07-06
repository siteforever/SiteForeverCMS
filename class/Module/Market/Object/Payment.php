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
    protected static function doFields()
    {
        return array(
            new Field\IntField( 'id', 11, false, null, true ),
            new Field\VarcharField( 'name', 255, false, null, false ),
            new Field\TextField( 'desc', 11, false, null, false ),
            new Field\VarcharField( 'module', 255, false, null, false ),
            new Field\TinyintField( 'active', 1, false, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'payment';
    }
}

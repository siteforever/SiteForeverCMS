<?php
/**
 * Domain object Module
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property int id
 * @property string name
 * @property string path
 * @property string config
 * @property string desc
 * @property int pos
 * @property int active
 */
namespace Module\System\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Module extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField( 'id', 11, false, null, true ),
            new Field\VarcharField( 'name', 250, false, null, false ),
            new Field\VarcharField( 'path', 250, false, null, false ),
            new Field\BlobField( 'config', 11, false, null, false ),
            new Field\TextField( 'desc', 11, false, null, false ),
            new Field\IntField( 'pos', 11, false, null, false ),
            new Field\IntField( 'active', 11, false, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'module';
    }
}

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
            new Field\Int( 'id', 11, false, null, true ),
            new Field\Varchar( 'name', 250, false, null, false ),
            new Field\Varchar( 'path', 250, false, null, false ),
            new Field\Blob( 'config', 11, false, null, false ),
            new Field\Text( 'desc', 11, false, null, false ),
            new Field\Int( 'pos', 11, false, null, false ),
            new Field\Int( 'active', 11, false, null, false ),
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

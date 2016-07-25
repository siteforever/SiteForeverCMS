<?php
/**
 * Domain object Material
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property int id
 * @property string name
 * @property string image
 * @property int active
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Material extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField( 'id', 11, false, null, true ),
            new Field\VarcharField( 'name', 255, true, null, false ),
            new Field\VarcharField( 'image', 255, true, null, false ),
            new Field\IntField( 'active', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'material';
    }
}

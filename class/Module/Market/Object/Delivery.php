<?php
/**
 * Domain object Delivery
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Class Delivery
 * @package Module\Market\Object
 *
 * @property $id
 * @property $name
 * @property $desc
 * @property $cost
 * @property $active
 * @property $pos
 */
class Delivery extends Object
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
            new Field\TextField( 'desc', 11, true, null, false ),
            new Field\DecimalField( 'cost', 13, true, null, false ),
            new Field\TinyintField( 'active', 1, true, null, false ),
            new Field\IntField( 'pos', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'delivery';
    }
}

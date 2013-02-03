<?php
/**
 * Domain object Delivery
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Delivery extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int( 'id', 11, false, null, true ),
            new Field\Varchar( 'name', 250, true, null, false ),
            new Field\Text( 'desc', 11, true, null, false ),
            new Field\Decimal( 'cost', 13, true, null, false ),
            new Field\Tinyint( 'active', 1, true, null, false ),
            new Field\Int( 'pos', 11, true, null, false ),
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

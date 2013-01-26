<?php
/**
 * Domain object Product_Type
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */
namespace Module\Catalog\Object;

/**
 * @property int id
 * @property string name
 * @property Collection Fields
 */
use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Type extends Object
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
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function getTable()
    {
        return 'product_type';
    }
}

<?php
/**
 * Domain object Product_Type
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */
namespace Module\Catalog\Object;

use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Data\Field\IntField;
use Sfcms\Data\Field\VarcharField;

/**
 * @property int $id
 * @property string $name
 * @property Collection|Field[] $Fields
 */
class Type extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new IntField('id', 11, false, null, true),
            new VarcharField('name', 250, true, null, false),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'product_type';
    }
}

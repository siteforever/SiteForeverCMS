<?php
/**
 * Domain object Product_Field
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */
/**
 * @property int id
 * @property int product_type_id
 * @property string type
 * @property string name
 * @property string unit
 */
namespace Module\Catalog\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field as DataField;

class Field extends Object
{
    public function getValue(Catalog $product)
    {
        $propertyModel = $this->getModel('ProductProperty');
        /** @var $property Property */
        $property = $propertyModel->findByPk(array($product->getId(), $this->getId()));
        if ($property) {
            return $property->value;
        }
        return null;
    }

    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new DataField\IntField( 'id', 11, false, null, true ),
            new DataField\IntField( 'product_type_id', 11, false, 0 ),
            new DataField\VarcharField( 'type', 250, false, '' ),
            new DataField\VarcharField( 'name', 250, false, '' ),
            new DataField\VarcharField( 'unit', 250, false, '' ),
            new DataField\IntField( 'pos', 11, false, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'product_field';
    }
}

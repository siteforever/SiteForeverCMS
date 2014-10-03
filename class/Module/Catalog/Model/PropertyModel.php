<?php
/**
 * Модель Product_Property
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

namespace Module\Catalog\Model;

use Module\Catalog\Object\Catalog;
use Module\Catalog\Object\Field;
use Module\Catalog\Object\Property;
use Sfcms\Model;

/**
 * @property $product_id int
 * @property $product_field_id int
 * @property $value_string varchar
 * @property $value_text blob
 * @property $value_int int
 * @property $value_datetime datetime
 */
class PropertyModel extends Model
{
    public function relation()
    {
        return array(
            'Field' => array(self::BELONGS, 'ProductField', 'product_field_id', 'order' => 'pos'),
        );
    }

    /**
     * @param Field $field
     *
     * @return Property
     */
    public function findProductPropertyByField(Catalog $product, Field $field)
    {
        return $this->find(
            'product_id = ? AND product_field_id = ?', array($product->id, $field->id)
        );
    }
}

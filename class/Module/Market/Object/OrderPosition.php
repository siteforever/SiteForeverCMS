<?php
/**
 * Позиция в заказе
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

/**
 * @property $id
 * @property $ord_id
 * @property $product_id
 * @property $articul
 * @property $details
 * @property $currency
 * @property $item
 * @property $cat_id
 * @property $count
 * @property $price
 * @property $status
 * @property $sum
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class OrderPosition extends Object
{
    public function getSum()
    {
        return $this->count * $this->price;
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, true, null, true),
            new Field\IntField('ord_id'),
            new Field\IntField('product_id'),
            new Field\VarcharField('articul', 250),
            new Field\TextField('details'),
            new Field\VarcharField('currency', 10),
            new Field\VarcharField('item', 10),
            new Field\IntField('cat_id'),
            new Field\DecimalField('price'),
            new Field\IntField('count'),
            new Field\IntField('status'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'order_pos';
    }

    public static function keys()
    {
        return array(
            'idx_ord_id' => 'ord_id',
            'idx_cat_id' => 'cat_id',
            'idx_articul' => 'articul',
        );
    }
}

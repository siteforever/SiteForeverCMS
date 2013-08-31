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
            new Field\Int('id', 11, true, null, true),
            new Field\Int('ord_id'),
            new Field\Int('product_id'),
            new Field\Varchar('articul', 250),
            new Field\Text('details'),
            new Field\Varchar('currency', 10),
            new Field\Varchar('item', 10),
            new Field\Int('cat_id'),
            new Field\Decimal('price'),
            new Field\Int('count'),
            new Field\Int('status'),
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
            'ord_id',
            'cat_id',
            'articul',
        );
    }
}

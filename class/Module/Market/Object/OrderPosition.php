<?php
/**
 * Позиция в заказе
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

/**
 * @property $summa
 * @property $count
 * @property $price
 * @property $articul
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;
use Sfcms\Form\Form;

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
    protected static function doGetFields()
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
    public static function getTable()
    {
        return 'order_pos';
    }

    protected function getKeys()
    {
        return array(
            'ord_id',
            'cat_id',
            'articul',
        );
    }
}

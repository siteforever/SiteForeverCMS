<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class OrderStatus extends Object
{

    /**
     * Создаст список полей
     * @return array
     */
    protected static function doGetFields()
    {
        return array(
            new Field\Int('id', 11, true, null, true),
            new Field\Varchar('name', 100),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function getTable()
    {
        return 'order_status';
    }
}

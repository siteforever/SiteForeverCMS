<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field as DataField;

class Comment extends Object
{
    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new DataField\Int('id', 11, true, null, true),
            new DataField\Int('product_id'),
            new DataField\Varchar('ip', 15),
            new DataField\Varchar('name', 100),
            new DataField\Varchar('email', 100),
            new DataField\Varchar('phone', 100),
            new DataField\Varchar('subject', 100),
            new DataField\Text('content'),
            new DataField\Datetime('createdAt'),
            new DataField\Datetime('updatedAt'),
            new DataField\Tinyint('hidden', 1, true, '0'),
            new DataField\Tinyint('deleted', 1, true, '0'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'catalog_comment';
    }
}

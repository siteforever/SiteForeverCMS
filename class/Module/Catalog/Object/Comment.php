<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field as DataField;

/**
 * Class Comment
 * @package Module\Catalog\Object
 *
 * @property $id
 * @property $product_id
 * @property $ip
 * @property $name
 * @property $email
 * @property $phone
 * @property $subject
 * @property $content
 * @property $createdAt
 * @property $updatedAt
 * @property $hidden
 * @property $deleted
 */
class Comment extends Object
{
    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new DataField\IntField('id', 11, true, null, true),
            new DataField\IntField('product_id'),
            new DataField\VarcharField('ip', 15),
            new DataField\VarcharField('name', 100),
            new DataField\VarcharField('email', 100),
            new DataField\VarcharField('phone', 100),
            new DataField\VarcharField('subject', 100),
            new DataField\TextField('content'),
            new DataField\DatetimeField('createdAt'),
            new DataField\DatetimeField('updatedAt'),
            new DataField\TinyintField('hidden', 1, true, '0'),
            new DataField\TinyintField('deleted', 1, true, '0'),
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

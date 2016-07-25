<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

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
            new Field\IntField('id', 11, true, null, true),
            new Field\IntField('product_id'),
            new Field\VarcharField('ip', 15),
            new Field\VarcharField('name', 100),
            new Field\VarcharField('email', 100),
            new Field\VarcharField('phone', 100),
            new Field\VarcharField('subject', 100),
            new Field\TextField('content'),
            new Field\DatetimeField('createdAt'),
            new Field\DatetimeField('updatedAt'),
            new Field\TinyintField('hidden', 1, true, '0'),
            new Field\TinyintField('deleted', 1, true, '0'),
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

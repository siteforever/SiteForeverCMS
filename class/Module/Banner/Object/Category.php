<?php
/**
 * Объект категории баннера
 * @author Voronin Vladimir (voronin@stdel.ru)
 */
namespace Module\Banner\Object;

use Sfcms;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Class Category
 * @package Module\Banner\Object
 * @property $id
 * @property $name
 * @property $deleted
 */
class Category extends Object
{
    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'category_banner';
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, true, null, true),
            new Field\VarcharField('name', 255),
            new Field\IntField('deleted', 1, false, 0),
        );
    }
}

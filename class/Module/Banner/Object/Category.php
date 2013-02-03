<?php
/**
 * Объект категории баннера
 * @author Voronin Vladimir (voronin@stdel.ru)
 */
namespace Module\Banner\Object;

use Sfcms;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

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
            new Field\Int('id', 11, true, null, true),
            new Field\Varchar('name', 255),
        );
    }
}
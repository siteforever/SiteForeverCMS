<?php
/**
 * Объект новостной категории
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

/**
 * @property $id
 * @property $name
 * @property $description
 * @property $show_content
 * @property $show_list
 * @property $type_list
 * @property $per_page
 * @property $hidden
 * @property $protected
 * @property $deleted
 */
namespace Module\News\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Category extends Object
{

    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, true, null, true),
            new Field\VarcharField('name', 250),
            new Field\TextField('description'),
            new Field\TinyintField('show_content', 1),
            new Field\TinyintField('show_list', 1),
            new Field\TinyintField('type_list', 1),
            new Field\TinyintField('per_page', 1),
            new Field\TinyintField('hidden', 1),
            new Field\TinyintField('protected', 1),
            new Field\TinyintField('deleted', 1, true, 0),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'news_cats';
    }
}

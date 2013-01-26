<?php
/**
 * Объект Шаблона
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\System\Object;
 
use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Template extends Object
{
    /**
     * Вернет список полей
     * @return array
     */
    protected static function doGetFields()
    {
        return array(
            new Field\Varchar('name', 100, true),
            new Field\Varchar('description', 250),
            new Field\Text('template'),
            new Field\Int('update'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function getTable()
    {
        return 'templates';
    }

    protected function getPk()
    {
        return 'name';
    }
}

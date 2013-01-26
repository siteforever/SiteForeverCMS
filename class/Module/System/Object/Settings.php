<?php
/**
 * Объект Настройки
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\System\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Settings extends Object
{
    /**
     * Создаст список полей
     * @return array
     */
    protected static function doGetFields()
    {
        return array(
            //new Field\Int('id', 11, true, null, true),
            new Field\Varchar('module', 100),
            new Field\Varchar('property', 100),
            new Field\Varchar('value', 100),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function getTable()
    {
        return 'settings';
    }

    protected function getPk()
    {
        return 'module,property';
    }
}

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
    protected static function doFields()
    {
        return array(
            //new Field\Int('id', 11, true, null, true),
            new Field\VarcharField('module', 100),
            new Field\VarcharField('property', 100),
            new Field\VarcharField('value', 100),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'settings';
    }

    public static function pk()
    {
        return 'module,property';
    }
}

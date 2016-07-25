<?php
/**
 * Маршрут
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
namespace Module\System\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Routes extends Object
{

    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, false, null, true),
            new Field\IntField('pos'),
            new Field\VarcharField('alias', 200),
            new Field\VarcharField('controller', 50, true, 'page'),
            new Field\VarcharField('action', 50, true, 'index'),
            new Field\TinyintField('active'),
            new Field\TinyintField('protected'),
            new Field\TinyintField('system'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'routes';
    }
}

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
            new Field\Int('id', 11, false, null, true),
            new Field\Int('pos'),
            new Field\Varchar('alias', 200),
            new Field\Varchar('controller', 50, true, 'page'),
            new Field\Varchar('action', 50, true, 'index'),
            new Field\Tinyint('active'),
            new Field\Tinyint('protected'),
            new Field\Tinyint('system'),
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

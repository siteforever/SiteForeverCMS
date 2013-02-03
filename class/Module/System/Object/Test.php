<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

namespace Module\System\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Test extends Object
{

    /**
     * Создаст список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int('id',11,true,null,true),
            new Field\Varchar('value'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'test';
    }
}

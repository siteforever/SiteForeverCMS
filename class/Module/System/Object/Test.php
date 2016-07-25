<?php
/**
 *
 * @author Nikolay Ermin <keltanas@gmail.com>
 * @link http://siteforever.ru
 */

namespace Module\System\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Class Test
 * @package Module\System\Object
 *
 * @property $id
 * @property $value
 */
class Test extends Object
{
    /**
     * Создаст список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id',11,true,null,true),
            new Field\VarcharField('value'),
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

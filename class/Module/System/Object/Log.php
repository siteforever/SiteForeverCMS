<?php
/**
 * Объект записи журнала
 * @author: keltanas
 * @link http://siteforever.ru
 */
namespace Module\System\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Class Log
 * @package Module\System\Object
 *
 * @property $id
 * @property $user
 * @property $object
 * @property $object_id
 * @property $action
 * @property $timestamp
 */
class Log extends Object
{
    protected static $engine   = 'MyISAM';

    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField( 'id', 11, false, null, true ),
            new Field\IntField( 'user', 11, true, null, false ),
            new Field\VarcharField( 'object', 250, true, null, false ),
            new Field\VarcharField( 'object_id', 250, true, null, false ),
            new Field\VarcharField( 'action', 250, true, null, false ),
            new Field\IntField( 'timestamp', 11, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'log';
    }
}

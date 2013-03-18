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
 * @property $action
 * @property $timestamp
 */
class Log extends Object
{
    protected $engine   = 'MyISAM';

    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int( 'id', 11, false, null, true ),
            new Field\Int( 'user', 11, true, null, false ),
            new Field\Varchar( 'object', 250, true, null, false ),
            new Field\Varchar( 'action', 250, true, null, false ),
            new Field\Int( 'timestamp', 11, true, null, false ),
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

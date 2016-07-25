<?php
/**
 * Сессия
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Class Session
 * @package Module\System\Object
 *
 * @property string $sess_id
 * @property string $sess_data
 * @property int $sess_time
 */
class Session extends Object
{
    protected static function doFields()
    {
        return array(
            new Field\VarcharField('sess_id', 26, true, ''),
            new Field\TextField('sess_data'),
            new Field\IntField('sess_time', 11, true, 0),
        );
    }

    public static function pk()
    {
        return 'sess_id';
    }

    public static function table()
    {
        return 'session';
    }
}

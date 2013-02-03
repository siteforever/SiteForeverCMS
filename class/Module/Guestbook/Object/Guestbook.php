<?php
/**
 * Domain ovject Guestbook
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property int id
 * @property int link
 * @property string name
 * @property string email
 * @property string site
 * @property string city
 * @property int date
 * @property string ip
 * @property string message
 */

namespace Module\Guestbook\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Guestbook extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int( 'id', 11, false, null, true ),
            new Field\Int( 'link', 11, true, null, false ),
            new Field\Varchar( 'name', 250, true, null, false ),
            new Field\Varchar( 'email', 250, true, null, false ),
            new Field\Varchar( 'site', 250, true, null, false ),
            new Field\Varchar( 'city', 250, true, null, false ),
            new Field\Int( 'date', 11, true, null, false ),
            new Field\Varchar( 'ip', 15, true, null, false ),
            new Field\Text( 'message', 11, true, null, false ),
            new Field\Text( 'answer', 11, true, null, false ),
            new Field\Tinyint( 'hidden', 1, false, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'guestbook';
    }
}

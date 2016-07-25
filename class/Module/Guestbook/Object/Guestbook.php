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
            new Field\IntField( 'id', 11, false, null, true ),
            new Field\IntField( 'link', 11, true, null, false ),
            new Field\VarcharField( 'name', 250, true, null, false ),
            new Field\VarcharField( 'email', 250, true, null, false ),
            new Field\VarcharField( 'site', 250, true, null, false ),
            new Field\VarcharField( 'city', 250, true, null, false ),
            new Field\IntField( 'date', 11, true, null, false ),
            new Field\VarcharField( 'ip', 15, true, null, false ),
            new Field\TextField( 'message', 11, true, null, false ),
            new Field\TextField( 'answer', 11, true, null, false ),
            new Field\TinyintField( 'hidden', 1, false, 0 ),
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

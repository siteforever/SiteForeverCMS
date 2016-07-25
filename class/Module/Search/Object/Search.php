<?php
/**
 * Объект поиска
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Search\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Search extends Object
{
    public static function table()
    {
        return 'search';
    }

    protected static function doFields()
    {
        return array(
//            new Field\IntField('id'),
//            new Field\IntField('id'),
        );
    }

}

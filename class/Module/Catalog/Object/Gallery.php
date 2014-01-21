<?php
/**
 * Cataloge gallery object
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 *
 * @property $id
 * @property $cat_id
 * @property $image
 * @property $thumb
 * @property $hidden
 * @property $main
 */
namespace Module\Catalog\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field\Int;
use Sfcms\Data\Field\Varchar;
use Sfcms\Data\Field\Tinyint;

class Gallery extends Object
{
    /**
     * @return mixed
     */
    public function getThumb()
    {
        if (!empty($this->data['thumb'])) {
            return $this->data['thumb'];
        }
        return $this->image;
    }

    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Int('id', 11, true, null, true),
            new Int('cat_id'),
            new Varchar('uuid', 36),
            new Varchar('image', 250),
            new Varchar('thumb', 250),
            new Tinyint('hidden'),
            new Tinyint('main'),
            new Tinyint('pos'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'catalog_gallery';
    }
}

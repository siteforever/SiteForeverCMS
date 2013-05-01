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
use Sfcms\Data\Field;

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
            new Field\Int('id', 11, true, null, true),
            new Field\Int('cat_id'),
            new Field\Varchar('image', 250),
            new Field\Varchar('thumb', 250),
            new Field\Tinyint('hidden'),
            new Field\Tinyint('main'),
            new Field\Tinyint('pos'),
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

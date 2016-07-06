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
use Sfcms\Data\Field\IntField;
use Sfcms\Data\Field\VarcharField;
use Sfcms\Data\Field\TinyintField;

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
            new IntField('id', 11, true, null, true),
            new IntField('cat_id'),
            new VarcharField('uuid', 36),
            new VarcharField('image', 250),
            new VarcharField('thumb', 250),
            new TinyintField('hidden'),
            new TinyintField('main'),
            new TinyintField('pos'),
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

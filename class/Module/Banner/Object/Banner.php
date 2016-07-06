<?php
/**
 * Объект баннера
 *
 * @author Voronin Vladimir (voronin@stdel.ru)
 */
namespace Module\Banner\Object;

use Sfcms;
use Sfcms\Data\Exception;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Class Banner
 * @package Module\Banner\Object

 * @property $id
 * @property $cat_id
 * @property $name
 * @property $url
 * @property $path
 * @property $count_show
 * @property $count_click
 * @property $target
 * @property $content
 * @property $deleted
 */
class Banner extends Object
{
    /**
     * Вернет адрес перехода для баннера
     * @return string
     */
    public function getUrl()
    {
        if (isset($this->data['url'])) {
            if (preg_match('/^http/', $this->data['url'])) {
                $url = $this->data['url'];
            } else {
                $url = '/' . trim($this->data['url'], '/');
            }
            return $url;
        }
        return null;
    }

    public function getBlock()
    {
        if ( ! $this->getId() ) {
            throw new Exception('Identifier for banner not found. Banner does not exist.');
        }
        return Sfcms::html()->link( $this->content, 'banner/redirectbanner', array('htmlTarget'=>$this->target,'id'=>$this->getId()) );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'banner';
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, true, null, true),
            new Field\IntField('cat_id'),
            new Field\VarcharField('name', 255),
            new Field\VarcharField('url', 255),
            new Field\VarcharField('path', 255),
            new Field\IntField('count_show'),
            new Field\IntField('count_click'),
            new Field\VarcharField('target', 255),
            new Field\TextField('content'),
            new Field\IntField('deleted', 1, false, 0),
        );
    }
}

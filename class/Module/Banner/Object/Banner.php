<?php
/**
 * Объект баннера
 * @author Voronin Vladimir (voronin@stdel.ru)
 *
 *
 * @property $id
 * @property $cat_id
 * @property $name
 * @property $url
 * @property $path
 * @property $count_show
 * @property $count_click
 * @property $target
 * @property $content
 */
namespace Module\Banner\Object;

use Sfcms;
use Sfcms\Data\Exception;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Banner extends Object
{
    /**
     * Вернет адрес перехода для баннера
     * @return string
     */
    public function getUrl()
    {
        if ( preg_match('/^http/', $this->data['url']) ) {
            $url = $this->data['url'];
        } else {
            $url =  'http' . ( isset( $_SERVER[ 'HTTPS' ] ) && 'off' !== $_SERVER['HTTPS'] ? "s" : "" ) . '://'
                           . $_SERVER[ "HTTP_HOST" ] . $this->data['url'];
        }
        return $url;
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
    public static function getTable()
    {
        return 'banner';
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected static function doGetFields()
    {
        return array(
            new Field\Int('id', 11, true, null, true),
            new Field\Int('cat_id'),
            new Field\Varchar('name', 255),
            new Field\Varchar('url', 255),
            new Field\Varchar('path', 255),
            new Field\Int('count_show'),
            new Field\Int('count_click'),
            new Field\Varchar('target', 255),
            new Field\Text('content'),
        );
    }
}

<?php
/**
 * Объект Новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

/**
 * @property $id
 * @property $cat_id
 * @property $author_id
 * @property $alias
 * @property $name
 * @property $notice
 * @property $text
 * @property $date
 * @property $title
 * @property $keywords
 * @property $description
 * @property $priority
 * @property $hidden
 * @property $protected
 * @property $deleted
 */
namespace Module\News\Object;

use Module\Page\Object\Page;
use Sfcms;
use Module\Page\Model\PageModel;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

class News extends Object
{
    public function getAlias()
    {
        if (empty($this->data['alias']) || '0' == $this->data['alias']{0}) {
            $this->data['alias'] = $this->name
                ? strtolower($this->id . '-' . Sfcms::i18n()->translit($this->name))
                : $this->id;
            $this->changed['alias'] = true;
        }
        return $this->data['alias'];
    }

    public function onSetName()
    {
        // todo как-то переписать этот метод
        $this->data['alias'] = null;
        $this->getAlias();
    }

    public function getTitle()
    {
        if ( ! empty( $this->data['title'] ) ) {
            return $this->data['title'];
        }
        return $this->data['name'];
    }

    public function getUrl()
    {
        /** @var $pageModel PageModel */
        $pageModel = $this->getModel('Page');
        /** @var $page Page */
        $page = $pageModel->findByControllerLink( 'news', $this->cat_id );
        if ( null !== $page ) {
            return  $page->alias . '/' . $this->getAlias();
        } else {
            return $this->getAlias();
        }
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
            new Field\Int('author_id'),
            new Field\Varchar('alias', 250,true,''),
            new Field\Varchar('name', 250),
            new Field\Varchar('image', 250, false, ''),
            new Field\Tinyint('main', 1, false, 0),
            new Field\Tinyint('priority', 1, false, 0),
            new Field\Int('date'),

            new Field\Text('notice'),
            new Field\Text('text'),

            new Field\Varchar('title', 250),
            new Field\Varchar('keywords', 250),
            new Field\Varchar('description', 250),

            new Field\Tinyint('hidden', 1, false, 0),
            new Field\Tinyint('protected', 1, false, 0),
            new Field\Tinyint('deleted', 1, false, 0),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'news';
    }
}

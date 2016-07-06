<?php
/**
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Module\News\Object;

use Module\Page\Object\Page;
use Sfcms;
use Module\Page\Model\PageModel;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Объект Новостей
 *
 * @property $id
 * @property $cat_id
 * @property $author_id
 * @property $alias
 * @property $name
 * @property $notice
 * @property $text
 * @property $date
 * @property $note
 * @property $title
 * @property $keywords
 * @property $description
 * @property $priority
 * @property $hidden
 * @property $protected
 * @property $deleted
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property Category $Category
 */
class News extends Object
{
    public function getTitle()
    {
        if (!empty($this->data['title'])) {
            return $this->data['title'];
        }

        return $this->data['name'];
    }

    public function getUrl()
    {
        /** @var $pageModel PageModel */
        $pageModel = $this->getModel('Page');
        /** @var $page Page */
        $page = $pageModel->findByControllerLink('news', $this->cat_id);
        return $page ? $page->alias : 'news';
   }

    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, true, null, true),
            new Field\IntField('cat_id'),
            new Field\IntField('author_id'),

            new Field\VarcharField('alias', 250,true,''),
            new Field\VarcharField('name', 250),
            new Field\VarcharField('image', 250, false, ''),
            new Field\TinyintField('main', 1, false, 0),
            new Field\TinyintField('priority', 1, false, 0),

            new Field\IntField('date'),

            new Field\TextField('notice'),
            new Field\TextField('text'),

            new Field\VarcharField('title', 250),
            new Field\VarcharField('keywords', 250),
            new Field\VarcharField('description', 250),

            new Field\VarcharField('note', 250),

            new Field\TinyintField('hidden', 1, false, 0),
            new Field\TinyintField('protected', 1, false, 0),

            new Field\DatetimeField('created_at'),
            new Field\DatetimeField('updated_at'),

            new Field\TinyintField('deleted', 1, false, 0),
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

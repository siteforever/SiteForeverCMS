<?php
/**
 * Объект Страницы
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
namespace Module\Page\Object;

use Sfcms;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Class Page
 * @package Module\Page\Object
 *
 * @property int $id INT(11) NOT NULL AUTO_INCREMENT
 * @property int $parent INT(11) NOT NULL DEFAULT 0
 * @property string $name VARCHAR(255) NOT NULL
 * @property string $template VARCHAR(50) NOT NULL DEFAULT 'inner'
 * @property string $alias VARCHAR(250) NOT NULL DEFAULT ''
 * @property string $path TEXT DEFAULT ''
 * @property int $date INT(11) NOT NULL DEFAULT 0
 * @property int $update INT(11) NOT NULL DEFAULT 0
 * @property int $pos INT(11) NOT NULL DEFAULT 0
 * @property int $link INT(11) NOT NULL DEFAULT 0
 * @property string $controller VARCHAR(20) DEFAULT 'page'
 * @property string $action VARCHAR(20) DEFAULT 'index'
 * @property string $sort VARCHAR(20) DEFAULT 'pos ASC'
 * @property string $title VARCHAR(80) DEFAULT ''
 * @property string $notice TEXT DEFAULT ''
 * @property string $content TEXT DEFAULT ''
 * @property string $thumb VARCHAR(250) DEFAULT ''
 * @property string $image VARCHAR(250) DEFAULT ''
 * @property string $keywords VARCHAR(255) DEFAULT NULL
 * @property string $description VARCHAR(255) DEFAULT NULL
 * @property int $author INT(11) NOT NULL DEFAULT 0
 * @property int $hidden INT(1) NOT NULL DEFAULT 0
 * @property int $protected INT(1) NOT NULL DEFAULT 0
 * @property int $system INT(1) NOT NULL DEFAULT 0
 * @property int $deleted INT(1) NOT NULL DEFAULT 0
 * @property bool $active
 */
class Page extends Object
{
    /**
     * @return string
     */
    public function getUrl()
    {
        $alias = $this->alias;
        if ($this->link && 'page' == $this->controller) {
            $obj = $this->getModel()->findByPk($this->link);
            if ($obj) {
                $alias = $obj->getUrl();
            }
        }
        if ('index' === $alias) {
            $alias = '';
        }
        return $alias;
    }

    /**
     * Вернет ссылку на редактирования модуля в админке
     * @return null|string
     * @todo Зависимость от всех модулей. Надо переделать
     */
    public function getLinkEdit()
    {
        $link = null;
        $linkUrl = null;
        switch ( $this->controller ) {
            case 'catalog':
                $linkUrl = Sfcms::html()->url('catalog/category', array('edit'=>$this->link));
                break;
            case 'gallery':
                $linkUrl = Sfcms::html()->url('gallery/editcat', array('id'=>$this->link));
                break;
            case 'news':
                $linkUrl = Sfcms::html()->url('news/catedit', array('id'=>$this->link));
                break;
        }
        if ( $linkUrl ) {
            $link = Sfcms::html()->link( Sfcms::html()->icon( 'link', $this->t('Go to the module') ), $linkUrl );
        }
        return $link;
    }


    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getTitle()
    {
        if (!empty($this->data['title'])) {
            return $this->data['title'];
        }
        return $this->data['name'];
    }

    public function getKeywords()
    {
        if (!empty($this->data['keywords'])) {
            return $this->data['keywords'];
        }
        return join(', ', $this->getKeywordsList(4));
    }

    public function getKeywordsList($bound = 3)
    {
        $content = $this->content;
        $content = strip_tags($content);
        $content = mb_strtoupper($content);
        $content = preg_replace('/[^0-9A-ZА-Я ]/mui', ' ', $content);
        $content = preg_replace('/\s+/mui', ' ', $content);
        $words = explode(' ', $content);
        $words = array_filter($words, function($word) use($bound) {
                return mb_strlen($word) >= $bound;
            });
        $index = array();
        foreach ($words as $word) {
            if (isset($index[$word])) {
                $index[$word]['weight']++;
            } else {
                $index[$word] = array('word'=>$word, 'weight'=>1);
            }
        }
        $index = array_filter($index, function($item){
                return $item['weight'] > 1;
            });
        uasort($index, function($item1, $item2){
                return $item1['weight'] - $item2['weight'];
            });
        $result = array();
        foreach ($index as $item) {
            $result[mb_strtolower($item['word'])] = $item['weight'];
        }
        return array_keys($result);
    }

    /**
     * Делаем активной страницу и всех ее родителей
     * @param int $active
     */
    public function setActive($active = 1)
    {
        $this->data['active'] = $active;
        /** @var self $parent */
        if ($this->parent && $parent = $this->getModel()->find($this->parent)) {
            $parent->setActive($active);
        }
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'structure';
    }

    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, true, null, true),
            new Field\IntField('parent', 11, true, '0'),
            new Field\VarcharField('name', 80, true, ''),
            new Field\VarcharField('template', 50, true, 'inner'),
            new Field\VarcharField('alias', 250, true, ''),
            new Field\TextField('path'),
            new Field\IntField('date', 11, true, '0'),
            new Field\IntField('update', 11, true, '0'),
            new Field\IntField('pos', 11, true, '0'),
            new Field\IntField('link', 11, true, '0'),
            new Field\VarcharField('controller', 20, true, 'page'),
            new Field\VarcharField('action', 20, true, 'index'),
            new Field\VarcharField('sort', 20, true, 'pos ASC'),
            new Field\VarcharField('title', 80, true, ''),
            new Field\TextField('notice'),
            new Field\TextField('content'),
            new Field\VarcharField('thumb', 250, true, ''),
            new Field\VarcharField('image', 250, true, ''),
            new Field\VarcharField('keywords', 120, true, ''),
            new Field\VarcharField('description', 120, true, ''),
            new Field\IntField('author', 11, true, '0'),
            new Field\TinyintField('nofollow', 1, true, '0'),
            new Field\TinyintField('hidden', 1, true, '0'),
            new Field\TinyintField('protected', 1, true, '0'),
            new Field\TinyintField('system', 1, true, '0'),
            new Field\TinyintField('deleted', 1, true, '0'),
        );
    }

    public static function keys()
    {
        return array(
            'alias'         => 'alias',
            'date'          => 'date',
            'order'         => array('parent','pos'),
        );
    }
}

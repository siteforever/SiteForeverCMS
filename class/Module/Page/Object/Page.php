<?php
/**
 * Объект Страницы
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru

 * @property int id
 * @property int parent
 * @property string name
 * @property string template
 * @property string alias
 * @property int alias_id
 * @property string path
 * @property int date
 * @property int update
 * @property int pos
 * @property int link
 * @property string controller
 * @property string action
 * @property string sort
 * @property string title
 * @property string notice
 * @property string content
 * @property string thumb
 * @property string image
 * @property string keywords
 * @property string description
 * @property int author
 * @property int hidden
 * @property int protected
 * @property int system
 * @property int deleted
 */
namespace Module\Page\Object;

use Sfcms;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Page extends Object
{
    /**
     * Вернет выделенный контент
     * @param array $words
     * @return array|Object|mixed|null
     */
    public function getHlContent( array $words )
    {
        $result = $this->get('content');
        foreach ( $words as $word ) {
            if ( strlen( $word ) > 3 ) {
                $result = str_ireplace( $word, '<b class="highlight">'.$word.'</b>', $result );
            }
        }
        return $result;
    }


    /**
     * @return string
     */
    public function getAlias()
    {
        if ( empty( $this->data['alias'] ) ) {
            $this->data['alias'] = trim( Sfcms::i18n()->translit(strtolower($this->data['name'])), '/ ' );
        }
        return $this->data['alias'];
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->alias;
    }

    /**
     * Вернет ссылку на редактирования модуля в админке
     * @return null|string
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
            $link = Sfcms::html()->link( Sfcms::html()->icon( 'link', t('Go to the module') ), $linkUrl );
        }
//        $link = "<a href='{$linkUrl}'>" . icon( 'link', 'Перейти к модулю' ) . '</a>';
        return $link;
    }


    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getTitle()
    {
        if ( $this->data['title'] ) {
            return $this->data['title'];
        }
        return $this->data['name'];
    }

    /**
     * Делаем активной страницу и всех ее родителей
     * @param int $active
     */
    public function setActive( $active = 1 )
    {
        $this->data['active'] = $active;
        if ( $parent = $this->getModel()->find( $this->parent ) ) {
            $parent->setActive($active);
        }
    }

    /**
     * Создаст serialize путь для конвертации в breadcrumbs
     * @return string
     */
    public function createPath()
    {
        $path   = array();
        $obj    = $this;
        while ( null !== $obj ) {
            $path[] = array(
                'id'    => $obj->getId(),
                'name'  => $obj->get('name'),
                'url'   => $obj->getAlias(),
            );
            $obj = $obj->parent ? $this->getModel()->find( $obj->parent ) : null;
        }
        $path   = array_reverse($path);
        return serialize($path);
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
            new Field\Int('id', 11, true, null, true),
            new Field\Int('parent', 11, true, '0'),
            new Field\Varchar('name', 80, true, ''),
            new Field\Varchar('template', 50, true, 'inner'),
            new Field\Varchar('alias', 250, true, ''),
            //            new Field\Int('alias_id', 11, true, '0'),
            new Field\Text('path'),
            new Field\Int('date', 11, true, '0'),
            new Field\Int('update', 11, true, '0'),
            new Field\Int('pos', 11, true, '0'),
            new Field\Int('link', 11, true, '0'),
            new Field\Varchar('controller', 20, true, 'page'),
            new Field\Varchar('action', 20, true, 'index'),
            new Field\Varchar('sort', 20, true, 'pos ASC'),
            new Field\Varchar('title', 80, true, ''),
            new Field\Text('notice'),
            new Field\Text('content'),
            new Field\Varchar('thumb', 250, true, ''),
            new Field\Varchar('image', 250, true, ''),
            new Field\Varchar('keywords', 120, true, ''),
            new Field\Varchar('description', 120, true, ''),
            new Field\Int('author', 11, true, '0'),
            new Field\Tinyint('nofollow', 1, true, '0'),
            new Field\Tinyint('hidden', 1, true, '0'),
            new Field\Tinyint('protected', 1, true, '0'),
            new Field\Tinyint('system', 1, true, '0'),
            new Field\Tinyint('deleted', 1, true, '0'),
        );
    }

    public static function keys()
    {
        return array(
            'id_structure'  => 'parent',
            'alias'         => 'alias',
            'date'          => 'date',
            'order'         => array('parent','pos'),
            'request'       => 'alias'
        );
    }
}

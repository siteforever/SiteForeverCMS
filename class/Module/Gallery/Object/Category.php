<?php
/**
 * Объект Категории Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */

/**
 * @property int id
 * @property string name
 * @property int middle_method
 * @property int middle_width
 * @property int middle_height
 * @property int thumb_method
 * @property int thumb_width
 * @property int thumb_height
 * @property string target
 * @property string thumb
 * @property int perpage
 * @property string color
 * @property string meta_description
 * @property string meta_keywords
 * @property string meta_h1
 * @property string meta_title
 */

namespace Module\Gallery\Object;

use App;
use Module\Page\Object\Page;
use Sfcms\Data\Object;
use Sfcms\Data\Field;
use Sfcms\Exception;

class Category extends Object
{
    /**
     * @var Page
     */
    private $_page = null;

    /**
     * Вернет псевдоним для категории
     * @return mixed|string
     */
    public function getAlias()
    {
        try {
            $strpage = $this->getPage();
        }
        catch ( Exception $e ) {
            return App::cms()->getRouter()->createServiceLink(
                'gallery', 'index', array( 'id'=> $this->getId() )
            );
        }

        if ( $strpage ) {
            return $strpage->get( 'alias' );
        }
        return '';
//        else {
//            return $alias_model->generateAlias( $this->get( 'name' ) );
//        }
    }

    /**
     * Вернет страницу, к которой привязана категория
     * @return Page
     */
    public function getPage()
    {
        if (null === $this->_page) {
            $model = $this->getModel( 'Page' );

            $this->_page = $model->find(
                array(
                    'cond'  => 'action = ? AND controller = ? AND link = ? AND deleted = 0 ',
                    'params'=> array( 'index', 'gallery', $this->getId() ),
                )
            );
            if (null === $this->_page) {
                throw new Exception( $this->t( 'Page not found for gallery' ) );
            }
        }
        return $this->_page;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        $image = null;
        if ($this->id) {
            $imageModel = $this->getModel('Gallery');
            $image = $imageModel->find(
                array(
                    'cond' => 'category_id = ? AND hidden != ?',
                    'params' => array($this->id, 1),
                    'order' => 'pos',
                )
            );
        }
        if ($image) {
            if (empty($this->data['image']) || $image->image != $this->data['image']) {
                $this->data['image'] = $image->image;
                $this->markDirty();
            }
        } else {
            $this->data['image'] = '';
        }
        return $this->data['image'];
    }

    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField( 'id', 11, false, null, true ),
            new Field\VarcharField( 'name', 250, true, null, false ),
            new Field\TinyintField( 'middle_method', 4, false, 1 ),
            new Field\IntField( 'middle_width', 11, false, 200 ),
            new Field\IntField( 'middle_height', 11, false, 200 ),
            new Field\TinyintField( 'thumb_method', 4, false, 1 ),
            new Field\IntField( 'thumb_width', 11, false, 100 ),
            new Field\IntField( 'thumb_height', 11, false, 100 ),
            new Field\VarcharField('target', 10),
            new Field\VarcharField('image', 250),
            new Field\IntField( 'perpage', 11, true, null, false ),
            new Field\VarcharField( 'color', 20, true, null, false ),
            new Field\TinyintField( 'deleted', 1, true, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'gallery_category';
    }
}

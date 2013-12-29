<?php
/**
 * Объект Изображения Галереи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Module\Gallery\Object;

use Module\Page\Model\PageModel;
use Sfcms\Data\Object;
use Sfcms\Data\Field;
use Sfcms;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @property int id
 * @property int category_id
 * @property string alias
 * @property string name
 * @property string link
 * @property string description
 * @property string image
 * @property string middle
 * @property string thumb
 * @property int pos
 * @property int main
 * @property int hidden
 * @property string desc
 * @property float cost
 * @property int active
 * @property string title
 * @property string h1
 * @property Category Category
 */
class Gallery extends Object
{
    /** @var UploadedFile */
    protected $file;

    /**
     * Set and move uploaded file
     * @param UploadedFile $file
     */
    public function setUploadedFile(UploadedFile $file)
    {
        $config = $this->app()->getContainer()->getParameter('gallery');
        $dest = $config['path'] . DS . substr('0000' . $this->category_id, -4, 4);
        if (!$this->getId()) {
            $this->save(true);
        }
        $this->file = $file->move(ROOT . $dest, $this->getId() . '_' . $file->getClientOriginalName());
        $this->image = $dest . '/' . $this->file->getBasename();
        if ( 0 == $this->pos ) {
            $this->Category->image = $this->image;
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        /** @var $pageModel PageModel */
        $pageModel = $this->getModel('Page');
        $page = $pageModel->findByControllerLink('gallery', $this->category_id);
        if ( null !== $page ) {
            return $page->alias . '/' . $this->alias;
        } else {
            return $this->alias;
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $title = '';
        if ( $this->name ) {
            $title = $this->name;
        }
        return $title;
    }

    public function getMiddle()
    {
        return $this->data['image'];
    }

    public function getThumb()
    {
        return $this->data['image'];
    }

    /**
     * Create field list
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int( 'id', 11, false, null, true ),
            new Field\Int( 'category_id', 11, true, null, false ),
            new Field\Varchar( 'alias', 250, true, null, false ),
            new Field\Varchar( 'name', 250, true, null, false ),
            new Field\Varchar( 'link', 250, true, null, false ),
            new Field\Text( 'description', 11, true, null, false ),
            new Field\Varchar( 'image', 250, true, null, false ),
            new Field\Int( 'pos', 11, true, null, false ),
            new Field\Tinyint( 'main', 1, true, 0 ),
            new Field\Tinyint( 'hidden', 1, true, 0 ),
            new Field\Tinyint( 'deleted', 1, true, 0 ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function table()
    {
        return 'gallery';
    }
}

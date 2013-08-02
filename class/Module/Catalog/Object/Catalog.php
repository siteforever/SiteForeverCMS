<?php
/**
 * Объект Каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\Catalog\Object;

use Module\Catalog\Model\CatalogModel;
use Module\Market\Object\Manufacturer;
use Module\Page\Model\PageModel;
use Module\Page\Object\Page;
use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Data\Field;
use Sfcms\i18n;

/**
 * Class Catalog
 * @package Module\Catalog\Object
 *
 * @property int $id
 * @property int $parent
 * @property string $uuid
 * @property string $parent_uuid
 * @property int $cat
 * @property int $pos
 * @property int $manufacturer
 * @property int $material
 * @property string $alias
 * @property string $url
 * @property string $name
 * @property string $full_name
 * @property string $unit
 * @property string $title
 * @property string $path
 * @property int $sale
 * @property int $sale_start
 * @property int $sale_stop
 * @property int $salePrice
 * @property int $gender
 * @property int $hidden
 * @property int $protected
 * @property int $deleted
 * @property Catalog Category
 * @property Collection Goods
 * @property Collection Comments
 * @property Manufacturer Manufacturer
 * @property Gallery Gallery
 * @property Page Page
 * @property Collection Properties
 * @property Type Type
 */
class Catalog extends Object
{
//    protected $_gallery = null;

    protected $_image   = null;

    public function __toString()
    {
        return $this->name;
    }


    /**
     * Вернет path для текущего объекта
     * @return string
     */
    public function path()
    {
        if ( $this->get('path') && $path = @unserialize($this->get('path')) ) {
            if ( is_array( $path ) ) {
                return $this->get('path');
            }
        }
        /** @var $model CatalogModel */
        $model = $this->getModel('Catalog');
        $path = $model->createSerializedPath( $this->getId() );
        return $path;
    }

    /**
     * Вернет цену продукта в зависимости от привелегий пользователя
     * @param bool $wholesale Вернуть розничную цену
     * @return float
     */
    public function getPrice( $wholesale = false )
    {
        if ( $wholesale && $this->get('price2') > 0 ) {
            return $this->get('price2');
        }
        return $this->get('price1');
    }

    /**
     * Вернет цену с учетом скидки
     * @return float|null
     */
    public function getSalePrice()
    {
        if ( $this->sale ) {
            $start = mktime( 0,0,0,date('n',$this->data['sale_start']),date('d',$this->data['sale_start']),date('Y',$this->data['sale_start']) );
            $stop  = mktime( 23,59,59,date('n',$this->data['sale_stop']),date('d',$this->data['sale_stop']),date('Y',$this->data['sale_stop']) );
            if ( $start <= time() && time() <= $stop ) {
                return ceil( $this->getPrice() * ( 100 - $this->sale ) / 1000 ) * 10;
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return empty($this->data['title']) ? $this->name : $this->data['title'];
    }

    /**
     * Вернет алиас товара
     * @return string
     */
    public function getAlias()
    {
        $alias = mb_strtolower(($this->cat ? '' : $this->id . '-') . $this->app()->getContainer()->get('i18n')->translit($this->name)) ?: $this->id;
        $alias = trim($alias, '-');
        if (empty($this->data['alias']) || $this->data['alias'] != $alias) {
            $this->data['alias'] = $alias;
            if (!$this->isStateCreate()) {
                $this->changed['alias'] = true;
            }
            if ($this->isStateClean()) {
                $this->markDirty();
            }
        }
        return $this->data['alias'];
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getUrl()
    {
        /** @var $modelPage PageModel */
        $modelPage = $this->getModel('Page');
        /** @var $page Page */
        $page = $modelPage->findByControllerLink('catalog', $this->cat ? $this->id : $this->parent);

        if ( null === $page ) {
            throw new \RuntimeException($this->t('Page for catalog category not found').'; page.link='.$this->parent);
        }

        return $page->alias . ($this->cat ? '' : '/' . $this->alias);
    }

    /**
     * Вернет главную картинку для товара
     * @return Gallery
     */
    public function getMainImage()
    {
        if (null === $this->_image) {
            $gallery = $this->Gallery;
//            var_dump($gallery);
            if (null === $gallery) {
                return null;
            }
            foreach ($gallery as $image) {
                if ($image->main == 1) {
                    $this->_image = $image;
                    break;
                }
            }
        }

        return $this->_image;
    }

    /**
     * Вернет строку для полной картинки с водяным знаком
     * @return string
     */
    public function getImage()
    {
        $image = $this->getMainImage();
        if ($image) {
            return $image->image;
        }

        return null;
    }

    /**
     * Вернет строку для полной картинки без водяного знака
     * @return string
     */
    public function getThumb()
    {
        $image = $this->getMainImage();
        if ($image) {
            return $image->thumb;
        }

        return null;
    }

    /**
     * Наименование дополнительного св-ва
     * @param $i
     *
     * @return string
     */
    public function getPropertyName( $i )
    {
        return $this->get('Category')->get('p'.$i);
    }

    /**
     * Значение дополнительного св-ва
     * @param $i
     *
     * @return array|Object|mixed|null
     */
    public function getPropertyValue( $i )
    {
        return $this->get('p'.$i);
    }

    /**
     * Вернет массив, описывающий св-во
     * @param $i
     *
     * @return array
     */
    public function getProperty( $i )
    {
        return array( 'name' => $this->getPropertyName($i), 'value' => $this->getPropertyValue($i) );
    }


    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int('id', 11, true, null, true),
            new Field\Int('parent'),
            new Field\Int('type_id', 11, true, 0),
            new Field\Tinyint('cat'),
            new Field\Varchar('uuid', 36),
            new Field\Varchar('parent_uuid', 36),
            new Field\Varchar('name', 100),
            new Field\Varchar('full_name', 100),
            new Field\Varchar('alias', 100),
            new Field\Varchar('unit', 20),
            new Field\Text('path'),
            new Field\Text('text'),
            new Field\Varchar('articul', 250),
            new Field\Decimal('price1'),
            new Field\Decimal('price2'),
            new Field\Int('material'),
            new Field\Int('manufacturer'),
            new Field\Int('pos'),
            new Field\Int('gender'),
            new Field\Varchar('p0', 250),
            new Field\Varchar('p1', 250),
            new Field\Varchar('p2', 250),
            new Field\Varchar('p3', 250),
            new Field\Varchar('p4', 250),
            new Field\Varchar('p5', 250),
            new Field\Varchar('p6', 250),
            new Field\Varchar('p7', 250),
            new Field\Varchar('p8', 250),
            new Field\Varchar('p9', 250),
            new Field\Tinyint('sort_view', 1, true, '1'),
            new Field\Int('sale', 1, true, '0'),
            new Field\Int('sale_start', 11, true, '0'),
            new Field\Int('sale_stop', 11, true, '0'),
            new Field\Tinyint('top', 11, true, '0'),
            new Field\Tinyint('novelty', 1, true, '0'),
            new Field\Tinyint('byorder', 1, true, '0'),
            new Field\Tinyint('absent', 1, true, '0'),
            new Field\Tinyint('hidden', 1, true, '0'),
            new Field\Tinyint('protected', 1, true, '0'),
            new Field\Tinyint('deleted', 1, true, '0'),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'catalog';
    }

    public static function keys()
    {
        return array(
            'showed' => array('deleted', 'hidden', 'cat'),
            'cat',
            'uuid',
            'parent_uuid',
            'alias',
            'hidden',
            'deleted',
        );
    }


}

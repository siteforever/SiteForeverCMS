<?php
/**
 * Объект Каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
namespace Module\Catalog\Object;

use Module\Catalog\Model\CatalogModel;
use Module\Catalog\Model\GalleryModel;
use Module\Market\Object\Manufacturer;
use Module\Page\Model\PageModel;
use Module\Page\Object\Page;
use Sfcms\Data\Collection;
use Sfcms\Data\Object;
use Sfcms\Data\Field;
use Sfcms\i18n;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        if ($this->get('path') && $path = @unserialize($this->get('path'))) {
            if (is_array($path)) {
                return $this->get('path');
            }
        }
        /** @var $model CatalogModel */
        $model = $this->getModel();
        $path = $model->createSerializedPath( $this->getId() );
        return $path;
    }

    /**
     * Вернет цену продукта в зависимости от привелегий пользователя
     * @param bool $wholesale Вернуть розничную цену
     * @return float
     */
    public function getPrice($wholesale = false)
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
        if ($this->isSale()) {
            if (preg_match('/(\d+)\s*%/', $this->sale, $m)) {
                return ceil($this->getPrice() * (100 - $m[1]) / 1000) * 10;
            }
            return $this->getPrice() - $this->sale;
        }

        return $this->getPrice();
    }

    public function isSale()
    {
        if ($this->sale) {
            $start = mktime(
                0, 0, 0,
                date('n', $this->data['sale_start']),
                date('d', $this->data['sale_start']),
                date('Y', $this->data['sale_start'])
            );
            $stop = mktime(
                23, 59, 59,
                date('n', $this->data['sale_stop']),
                date('d', $this->data['sale_stop']),
                date('Y', $this->data['sale_stop'])
            );
            if ($start <= time() && $stop >= time()) {
                return true;
            }
        }
        return false;
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
        if (empty($this->data['alias'])) {
            $alias = ($this->cat ? '' : $this->id . '-')
                    . $this->app()->getContainer()->get('i18n')->translit($this->name) ?: $this->id;
            $alias = substr(mb_strtolower(trim($alias, '-')), 0, 100);
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

        if (null === $page) {
            return $this->alias;
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
     * @param      $destDir
     * @param File|UploadedFile $file
     * @param Gallery $objImage
     * @param string $uuid
     *
     * @throws \RuntimeException
     */
    public function uploadImage($destDir, File $file, Gallery $objImage = null, $uuid = null)
    {
        if ($file instanceof UploadedFile
            && !in_array($file->getClientMimeType(), array('image/jpeg', 'image/gif', 'image/png'))
        ) {
            throw new \RuntimeException(sprintf('Unsupported image type "%s"', $file->getClientMimeType()));
        }

        /** @var Collection $images */
        if (!$this->Gallery) {
            $this->Gallery = new Collection();
        }
        $images = $this->Gallery;
        $createMain = !($images && $images->count()); // Делать ли первую картинку главной
        /** @var GalleryModel $galleryModel */
        $galleryModel = $this->getModel('CatalogGallery');

        if (null === $objImage) {
            $objImage = $galleryModel->createObject();
            $objImage->hidden = 0;
            $objImage->uuid   = $uuid ?: \Sfcms\UUID::v5(md5(__DIR__), bin2hex(uniqid()));;
            $objImage->pos    = $images->count();
            $objImage->main   = (int)$createMain;
            $objImage->trade  = $this;
            $objImage->cat_id = $this->id;
            $objImage->save(false, true);
        }
        $gId = $objImage->getId();

        $filesystem = new Filesystem();

        $dest = $destDir . '/'
            . substr(md5($this->id), 0, 2) . '/'
            . substr('000000' . $this->id, -6, 6) . '/';

        // Для thumb храним нормальное изображение в хэше, а для image накладываем watermark

        $ext = $file instanceof UploadedFile ? $file->getClientOriginalExtension() : $file->getExtension();

        // Имя не зашифровано, но с водяным знаком
        $img = strtolower($gId . '-' . $this->alias . '.' . $ext);
        // Это чистое изображение, но имя зашифровано
        $tmb = strtolower(substr(md5(microtime(1) . $this->alias), 0, 6) . '.' . $ext);

        try {
            /** @var File $target */
            if ($file instanceof UploadedFile) {
                $target = $file->move(ROOT . $dest, $tmb);
            } else {
                $filesystem->copy($file->getRealPath(), ROOT . $dest . $tmb, true);
                $target = new File(ROOT . $dest . $tmb, false);
            }
        } catch (FileException $e) {
            $objImage->delete();
            throw new \RuntimeException($e->getMessage());
        }

        $objImage->thumb = $dest . $tmb;
        $objImage->image = $dest . $img;
        if (!\Sfcms::watermark($target->getRealPath(), ROOT . $objImage->image)) {
            $filesystem->rename($target->getRealPath(), ROOT . $objImage->image, true);
            $objImage->thumb = $objImage->image;
        }
        $objImage->save();
        $this->Gallery->add($objImage);
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
            new Field\IntField('id', 11, true, null, true),
            new Field\IntField('parent'),
            new Field\IntField('type_id', 11, true, 0),
            new Field\TinyintField('cat'),
            new Field\VarcharField('uuid', 36),
            new Field\VarcharField('name', 100),
            new Field\VarcharField('title', 255),
            new Field\VarcharField('keywords', 255),
            new Field\TextField('description'),
            new Field\VarcharField('full_name', 100),
            new Field\VarcharField('alias', 100),
            new Field\VarcharField('unit', 20),
            new Field\TextField('path'),
            new Field\TextField('text'),
            new Field\VarcharField('articul', 250),
            new Field\DecimalField('price1'),
            new Field\DecimalField('price2'),
            new Field\IntField('material'),
            new Field\IntField('manufacturer'),
            new Field\IntField('pos'),
            new Field\IntField('gender'),
            new Field\IntField('qty', 11, true, 0),
            new Field\VarcharField('p0', 250),
            new Field\VarcharField('p1', 250),
            new Field\VarcharField('p2', 250),
            new Field\VarcharField('p3', 250),
            new Field\VarcharField('p4', 250),
            new Field\VarcharField('p5', 250),
            new Field\VarcharField('p6', 250),
            new Field\VarcharField('p7', 250),
            new Field\VarcharField('p8', 250),
            new Field\VarcharField('p9', 250),
            new Field\TinyintField('sort_view', 1, true, '1'),
            new Field\VarcharField('sale', 10, true, '0'),
            new Field\IntField('sale_start', 11, true, '0'),
            new Field\IntField('sale_stop', 11, true, '0'),
            new Field\TinyintField('top', 11, true, '0'),
            new Field\TinyintField('novelty', 1, true, '0'),
            new Field\TinyintField('byorder', 1, true, '0'),
            new Field\TinyintField('absent', 1, true, '0'),
            new Field\TinyintField('hidden', 1, true, '0'),
            new Field\TinyintField('protected', 1, true, '0'),
            new Field\TinyintField('deleted', 1, true, '0'),
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
            'showed' => array('cat', 'deleted', 'hidden'),
            'uuid' => 'uuid',
            'alias' => 'alias',
        );
    }
}

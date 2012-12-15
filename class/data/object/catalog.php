<?php
/**
 * Объект Каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 *
 * @property int $id
 * @property int $parent
 * @property int $cat
 * @property int $pos
 * @property int $manufacturer
 * @property int $material
 * @property string $alias
 * @property string $url
 * @property string $name
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
 * @property Data_Object_Catalog Category
 * @property Data_Collection Goods
 * @property Data_Object_Manufacturers Manufacturer
 * @property Data_Object_CatalogGallery Gallery
 * @property Data_Object_Page Page
 * @property Data_Collection Properties
 * @property Data_Object_ProductType Type
 */
class Data_Object_Catalog extends Data_Object
{
//    protected $_gallery = null;

    protected $_image   = null;

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
        $path = $this->getModel()->createSerializedPath( $this->getId() );
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
            //var_dump( strftime('%X %x' ,$start), strftime('%X %x' ,$stop) );
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
        return empty( $this->data['title'] ) ? $this->name : $this->data['title'];
    }

    /**
     * Вернет алиас товара
     * @return string
     */
    public function getAlias()
    {
        $alias = strtolower( $this->id . '-' . Sfcms_i18n::getInstance()->translit( $this->name ) ) ?: $this->id;
        if ( empty( $this->data['alias'] ) || $this->data['alias'] != $alias ) {
            $this->data['alias'] = $alias;
            $this->markDirty();
        }
        return $alias;
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function getUrl()
    {
        /** @var $modelPage Model_Page */
        $modelPage = $this->getModel('Page');
        /** @var $page Data_Object_Page */
        $page = $modelPage->findByControllerLink('catalog', $this->cat ? $this->id : $this->parent);

        if ( null === $page ) {
            throw new RuntimeException(t('Page for catalog category not found').'; page.link='.$this->parent);
        }

        return $page->alias . ( $this->cat ? '' : '/' .  $this->alias );
    }

    /**
     * Вернет главную картинку для товара
     * @return Data_Object_CatalogGallery
     */
    public function getMainImage()
    {
        if ( null === $this->_image ) {
            $gallery    = $this->Gallery;
            if ( null === $gallery ) {
                return null;
            }
            foreach ( $gallery as $image ) {
                if ( $image->main == 1 ) {
                    $this->_image   = $image;
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
        $image  = $this->getMainImage();
        if ( $image )
            return $image->image;
        return '';
    }

    /**
     * Вернет строку для полной картинки без водяного знака
     * @return string
     */
    public function getThumb()
    {
        $image  = $this->getMainImage();
        if ( $image )
            return $image->thumb;
        return '';
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
     * @return array|Data_Object|mixed|null
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
}

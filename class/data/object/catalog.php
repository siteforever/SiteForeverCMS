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
 * @property string $alias
 * @property string $url
 * @property string $name
 * @property string $title
 * @property string $path
 * @property int deleted
 * @property Data_Object_Manufacturers Manufacturer
 * @property Data_Object_CatalogGallery Gallery
 */
class Data_Object_Catalog extends Data_Object
{
    protected $_gallery = null;

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
     * @return float
     */
    public function getPrice()
    {
        $user = App::getInstance()->getAuth()->currentUser();
        if ( $user->getPermission() == USER_WHOLE && $this->get('price2') > 0 ) {
            return $this->get('price2');
        }
        return $this->get('price1');
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
        $alias = strtolower( Sfcms_i18n::getInstance()->translit( $this->name ) ) ?: $this->id;
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
        if ( $this->cat ) {
            $page = $modelPage->findByControllerLink('catalog', $this->id);
        } else {
            $page = $modelPage->findByControllerLink('catalog', $this->parent);
        }

        if ( null === $page ) {
            throw new RuntimeException(t('Page for catalog category not found').'; page.link='.$this->parent);
        }

        return $page->alias . ( $this->cat ? '' : '/' .  $this->alias );
    }

    /**
     * Вернет список изображений для товара
     * @return Data_Collection
     */
/*    public function getGallery()
    {
        if ( null === $this->_gallery && $this->getId() ) {
            $gallery_model  = $this->getModel('CatalogGallery');
            $this->_gallery = $gallery_model->findAll(array(
                 'cond'      => ' cat_id = ? ',
                 'params'    => array( $this->getId() ),
            ));
        }
        return $this->_gallery;
    }*/


    /**
     * @param $gallery
     */
    public function setGallery( $gallery )
    {
        $this->_gallery = $gallery;
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
     * Вернет строку для маленькой картинки
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
     * Вернет строку для средней картинки
     * @return string
     */
    public function getMiddle()
    {
        $image  = $this->getMainImage();
        if ( $image )
            return $image->middle;
        return '';
    }

    /**
     * Вернет строку для полной картинки
     * @return string
     */
    public function getImage()
    {
        $image  = $this->getMainImage();
        if ( $image )
            return $image->image;
        return '';
    }
}

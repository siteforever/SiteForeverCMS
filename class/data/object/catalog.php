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
 * @property string $name
 * @property string $path
 * @property int deleted
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

        $path = array();

        if( $this->getId() ) {
            $item = $this;
            while( $item ) {
                $path[ ] = array(
                    'id'  => $item->getId(),
                    'name'=> $item->name
                );
                if( $item->parent ) {
                    $item = $this->getModel( 'Catalog' )->find( $item->parent );
                } else {
                    $item = null;
                }
            }
        }

        $return = serialize( array_reverse( $path ) );
        $this->set('path', $return);
        $this->getModel()->save( $this );

        return $return;
    }

    /**
     * Вернет список изображений для товара
     * @return Data_Collection
     */
    public function getGallery()
    {
        if ( null === $this->_gallery && $this->getId() ) {
            $gallery_model  = $this->getModel('CatGallery');
            $this->_gallery = $gallery_model->findAll(array(
                 'cond'      => ' cat_id = ? ',
                 'params'    => array( $this->getId() ),
            ));
        }
        return $this->_gallery;
    }


    /**
     * @param $gallery
     */
    public function setGallery( $gallery )
    {
        $this->_gallery = $gallery;
    }

    /**
     * Вернет главную картинку для товара
     * @return Data_Object_CatGallery
     */
    public function getMainImage()
    {
        if ( null === $this->_image ) {
            $gallery    = $this->getGallery();
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

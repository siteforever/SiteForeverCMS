<?php
/**
 * Объект Каталога
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Data_Object_Catalog extends Data_Object
{
    protected $_gallery = null;

    protected $_image   = null;

    public function getGallery()
    {
        if ( null === $this->_gallery ) {
            $gallery_model  = $this->getModel('CatGallery');

            $this->_gallery = $gallery_model->findAll(array(
                 'cond'      => ' cat_id = ? ',
                 'params'    => array( $this->getId() ),
            ));
        }
        return $this->_gallery;
    }

    public function  getMainImage()
    {
        if ( null === $this->_image ) {
            $gallery    = $this->getGallery();
            foreach ( $gallery as $image ) {
                if ( $image->main == 1 ) {
                    $this->_image   = $image;
                    break;
                }
            }
        }
        return $this->_image;
    }

    public function getThumb()
    {
        $image  = $this->getMainImage();
        if ( $image )
            return $image->thumb;
        return '';
    }

    public function getMiddle()
    {
        $image  = $this->getMainImage();
        if ( $image )
            return $image->middle;
        return '';
    }

    public function getImage()
    {
        $image  = $this->getMainImage();
        if ( $image )
            return $image->image;
        return '';
    }
}

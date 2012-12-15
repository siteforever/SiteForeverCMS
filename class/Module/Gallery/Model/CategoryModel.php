<?php
namespace Module\Gallery\Model;

use Sfcms_Model;
use Forms_Gallery_Category;

class CategoryModel extends Sfcms_Model
{
    protected $form;


    public function tableClass()
    {
        return 'Data_Table_GalleryCategory';
    }

    public function objectClass()
    {
        return 'Data_Object_GalleryCategory';
    }

    /**
     * @return Gallery
     */
    function gallery()
    {
        return self::getModel('Gallery');
    }

    public function relation()
    {
        return array(
            'Images' => array( self::HAS_MANY, 'Gallery', 'category_id' ),
            'Page'   => array( self::HAS_ONE, 'Page', 'link' ),
        );
    }

    /**
     * Удаление категории
     * @param int $id
     * @return mixed
     */
    public function remove( $id )
    {
        /** @var Gallery $gallery */
        $category   = $this->find( $id );
        if ( $category ) {
            $images = $this->gallery()->findAll(array(
                'cond'      => 'category_id = :cat_id',
                'params'    => array(':cat_id'=>$category->getId()),
            ));
            foreach ( $images as $img ) {
                $this->gallery()->delete( $img['id'] );
            }
            $dir = ROOT . $this->config->get('gallery.dir')
                 . DIRECTORY_SEPARATOR.substr( '0000' . $category['id'], -4, 4 );
            @rmdir( $dir );
            $category->markDeleted();
        }

        return;
    }

    /**
     * @return Forms_Gallery_Category
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new Forms_Gallery_Category();
        }
        return $this->form;
    }
}

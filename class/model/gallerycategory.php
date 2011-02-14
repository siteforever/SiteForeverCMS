<?php

class model_GalleryCategory extends Model
{
    protected $form;

    /**
     * @return Model_Gallery
     */
    function gallery()
    {
        return self::getModel('Gallery');
    }

    /**
     * Удаление категории
     * @param  $id
     * @return
     */
    function remove( $id )
    {
        /**
         * @var model_gallery $gallery
         */
        $category   = $this->find( $id );

        if ( $category ) {

            $images = $this->gallery()->findAll(array(
                'cond'      => 'category_id = :cat_id',
                'params'    => array(':cat_id'=>$category->getId()),
            ));
            foreach ( $images as $img ) {
                $this->gallery()->delete( $img['id'] );
            }

            //print 'dir:'.ROOT.$this->config->get('gallery.dir').DS.substr( '0000'.$cat['id'], -4, 4 );
            $dir = ROOT.$this->config->get('gallery.dir').DS.substr( '0000'.$category['id'], -4, 4 );

            $this->app()->getLogger()->log('del: '.$id.' dir: '.$dir);

            //if ( file_exists( $dir ) ) {
            //}
            @rmdir( $dir );
            
            $category->markDeleted();
        }

        return;
    }

    /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_gallery_category();
        }
        return $this->form;
    }
}

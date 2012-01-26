<?php
/**
 * Модель категории баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */
 
class Model_CategoryBanner extends Sfcms_Model
{

    /**
     * @var Forms_Banners_CategoryBanner
     */
    protected $form = null;

     /**
     * Массив с категориями для select
     * @return array
     */
    function getCategoryBanner()
    {
        $parents = array();
        foreach( $this->findAll() as $branch ){
            $parents[$branch['id']] = $branch['name'];
        }
        return $parents;
    }

     /**
     * @return Forms_Banners_CategoryBanner
     */
    function getForm()
    {
        if ( null === $this->form ) {
            $this->form = new Forms_Banners_CategoryBanner();
        }
        return $this->form;
    }

     /**
     * Удаление категории
     * @param  $id
     * @return
     */
    function remove( $id )
    {
        /**
         * @var model_categorybanner $gallery
         */
        $category   = $this->find( $id );

        if ( $category ) {

            $images = $this->banner()->findAll(array(
                'cond'      => 'cat_id = :cat_id',
                'params'    => array(':cat_id'=>$category->getId()),
            ));
            foreach ( $images as $img ) {
                $this->banner()->delete( $img['id'] );
            }
//            $dir = ROOT.$this->config->get('gallery.dir').DIRECTORY_SEPARATOR.substr( '0000'.$category['id'], -4, 4 );
//            $this->app()->getLogger()->log('del: '.$id.' dir: '.$dir);
//            @rmdir( $dir );
            $category->markDeleted();
        }

        return;
    }

    /**
     * @return Model_Gallery
     */
    function banner()
    {
        return self::getModel('Banner');
    }

}

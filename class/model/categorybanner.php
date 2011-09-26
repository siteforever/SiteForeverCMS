<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 15.09.11
 * Time: 13:10
 * To change this template use File | Settings | File Templates.
 * Модель категории баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */
 
class Model_CategoryBanner extends Model
{
     /**
     * Массив с категориями для select
     * @return array
     */
    function getCategoryBanner()
    {
        foreach( $this->findAll() as $branch ){
            $parents[$branch['id']] = $branch['name'];
        }
        return $parents;
    }

     /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_banners_categorybanner();
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
            $this->app()->getLogger()->log('del: '.$id.' dir: '.$dir);
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

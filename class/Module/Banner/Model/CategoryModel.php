<?php
/**
 * Модель категории баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */

namespace Module\Banner\Model;

use Sfcms\Model;
use Forms_Banners_CategoryBanner;
use Module\Gallery\Model\GalleryModel;

class CategoryModel extends Model
{
    /**
     * @var Forms_Banners_CategoryBanner
     */
    protected $form = null;

     /**
     * Массив с категориями для select
     * @return array
     */
    public function getCategoryBanner()
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
    public function getForm()
    {
        if ( null === $this->form ) {
            $this->form = new Forms_Banners_CategoryBanner();
        }
        return $this->form;
    }

     /**
     * Удаление категории
     * @param  $id
     * @return void
     */
    public function remove( $id )
    {
        $category   = $this->find( $id );
        $modelBanner = self::getModel('Banner');

        if ( $category ) {
            $images = $modelBanner->findAll(array(
                'cond'      => 'cat_id = :cat_id',
                'params'    => array(':cat_id'=>$category->getId()),
            ));
            foreach ( $images as $img ) {
                $modelBanner->delete( $img['id'] );
            }
            $category->markDeleted();
        }

        return;
    }
}

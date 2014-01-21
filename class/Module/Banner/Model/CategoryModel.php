<?php
/**
 * Модель категории баннеров
 * @author Voronin Vladimir <voronin@stdel.ru>
 */

namespace Module\Banner\Model;

use Sfcms\Model;
use Module\Banner\Form\BannerCategoryForm;

class CategoryModel extends Model
{
    /**
     * @var BannerCategoryForm
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
     * @return BannerCategoryForm
     */
    public function getForm()
    {
        if ( null === $this->form ) {
            $this->form = new BannerCategoryForm();
        }
        return $this->form;
    }

     /**
     * Удаление категории
     * @param  $id
     * @return void
     */
    public function remove($id)
    {
        $category = $this->find($id);
        $modelBanner = self::getModel('Banner');

        if ( $category ) {
            $banners = $modelBanner->findAll(array(
                'cond'      => 'cat_id = :cat_id',
                'params'    => array(':cat_id'=>$category->getId()),
            ));
            foreach ($banners as $banner) {
                $banner->deleted = 1;
            }
            $category->deleted = 1;
        }

        return;
    }
}

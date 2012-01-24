<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 10.11.11
 * Time: 11:15
 * To change this template use File | Settings | File Templates.
 */ 
class Data_Object_GalleryCategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Data_Object_GalleryCategory
     */
    protected $gallery_cat;

    /**
     * @var Model_GalleryCategory
     */
    protected $model_cat;

    /**
     * @var Model_Gallery
     */
    protected $model;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->model_cat    = Sfcms_Model::getModel('GalleryCategory');
        $this->model        = Sfcms_Model::getModel('Gallery');

        $this->gallery_cat = $this->model_cat->createObject(
                    array(
                        'id'                => 1,
                        'name'              => 'System',
                        'middle_method'     => '1',
                        'middle_width'      => '110',
                        'middle_height'     => '110',
                        'thumb_method'      => '1',
                        'thumb_width'       => '110',
                        'thumb_height'      => '110',
                        '_target'           => '_self',
                        'perpage'           => '100',
                        'color'             => 'ffffff',
                        'meta_description'  => '',
                        'meta_keywords'     => '',
                        'meta_h1'           => '',
                        'meta_title'        => '',
                    )
                );
        $gallery    = $this->model->createObject(
            array(
                'id'            => 1,
                'category_id'   => 1,
                'name'          => 'Panasonic',
                'link'          => 'index',
                'description'   => '',
                'image'         => 'image',
                'middle'        => '',
                'thumb'         => 'ok',
                'pos'           => '',
                'main'          => '',
                'hidden'        => '',
            )
        );
    }

    public function testGetImage()
    {
        $this->assertEquals(
            '',
            $this->gallery_cat->getImage()
        );
    }
}

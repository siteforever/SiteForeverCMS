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
    protected $galleryCategory;

    /**
     * @var Model_GalleryCategory
     */
    protected $modelCategory;

    /**
     * @var Model_Gallery
     */
    protected $model;

    /**
     * @return void
     */
    protected function setUp()
    {
        Data_Watcher::instance()->clear();
        $this->modelCategory  = Sfcms_Model::getModel('GalleryCategory');
        $this->model          = Sfcms_Model::getModel('Gallery');

        $this->galleryCategory = $this->modelCategory->createObject(
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
            $this->galleryCategory->getImage()
        );
    }

    public function testGetAttributes()
    {
        $data = $this->galleryCategory->getAttributes();
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertEquals('System', $data['name']);
    }
}

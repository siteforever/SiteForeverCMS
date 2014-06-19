<?php
use Sfcms\Model;
use Module\Gallery\Object\Category;
use Module\Gallery\Model\CategoryModel;
use Module\Gallery\Model\GalleryModel;
use Sfcms\Data\Watcher;

class Object_GalleryCategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Category
     */
    protected $galleryCategory;

    /**
     * @var CategoryModel
     */
    protected $modelCategory;

    /**
     * @var GalleryModel
     */
    protected $model;

    /**
     * @return void
     */
    protected function setUp()
    {
        Watcher::instance()->clear();
        $this->modelCategory  = App::cms()->getModel('GalleryCategory');
        $this->model          = App::cms()->getModel('Gallery');

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

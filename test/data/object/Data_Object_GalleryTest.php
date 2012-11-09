<?php

class Data_Object_GalleryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Data_Object_Gallery
     */
    protected $gallery;

    /**
     * @return void
     */
    protected function setUp()
    {
        Data_Watcher::instance()->clear();
        $this->gallery    = Sfcms_Model::getModel('Gallery')->createObject(
            array(
                'id'            => 1,
                'category_id'   => 7,
                'name'          => 'Панасоник',
                'link'          => 'index',
                'description'   => '',
                'image'         => '',
                'middle'        => '',
                'thumb'         => '',
                'pos'           => '',
                'main'          => '',
                'hidden'        => '',
            )
        );
    }

    public function testGetCategory()
    {
        $this->assertInstanceOf('Data_Object_GalleryCategory', $this->gallery->Category);
    }

//    public function testGetAlias()
//    {
//        $this->assertEquals(
//            'supplies/panasonik',
//            $this->gallery->url
//        );
//    }
}
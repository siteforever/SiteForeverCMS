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
        $this->gallery    = Model::getModel('Gallery')->createObject(
            array(
                'id'            => 1,
                'category_id'   => 1,
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
        $this->assertInstanceOf('Data_Object_GalleryCategory', $this->gallery->getCategory());
    }

    public function testGetAlias()
    {
        $this->assertEquals(
            'supplies/panasonik',
            $this->gallery->getAlias()
        );
    }
}
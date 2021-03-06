<?php
use Module\Gallery\Object\Gallery;
use Module\Gallery\Object\Category;
use Sfcms\Data\Watcher;
use Sfcms\Model;

class GalleryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Gallery
     */
    protected $gallery;

    /**
     * @return void
     */
    protected function setUp()
    {
        Watcher::instance()->clear();
        $this->gallery    = App::cms()->getModel('Gallery')->createObject(
            array(
                'id'            => 1,
                'category_id'   => 7,
                'name'          => 'Панасоник',
                'link'          => 'index',
            )
        );
    }

    public function testGetCategory()
    {
        $this->assertInstanceOf('\Module\Gallery\Object\Category', $this->gallery->Category);
    }

    public function testGetAlias()
    {
        $this->gallery->save();
        $this->assertEquals(
            'portfolio/panasonik',
            $this->gallery->url
        );
    }
}

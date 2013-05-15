<?php
use Module\News\Object\News;
use Module\News\Model\NewsModel;
use Sfcms\Data\Watcher;
use Sfcms\Model;

class Object_NewsTest extends PHPUnit_Framework_TestCase
{
    /** @var News */
    protected $obj;
    /** @var NewsModel */
    protected $model;

    protected function setUp()
    {
        Watcher::instance()->clear();
        $this->model = Model::getModel('News');
        $this->obj   = $this->model->createObject(array('id'=>1));
    }

    public function testGetAlias()
    {
        $this->assertNotNull($this->obj, 'Object not found');
        $this->obj->name = 'Привет Мир!';
        $this->obj->alias = '';
        $this->assertEquals('1-privet-mir', $this->obj->getAlias());
    }

    public function testGetTitle()
    {
        $obj   = $this->model->createObject(
            array(
                'id'    => 100001,
                'name'  => 'Привет мир Yes!',
            )
        );
        $this->assertEquals('Привет мир Yes!', $obj->title);
        $obj->title = 'DEADBEEF!';
        $this->assertEquals('DEADBEEF!', $obj->title);
    }


}

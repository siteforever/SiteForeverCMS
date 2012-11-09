<?php

class Data_Object_NewsTest extends PHPUnit_Framework_TestCase
{
    /** @var Data_Object_News */
    protected $obj;
    /** @var Model_News */
    protected $model;

    protected function setUp()
    {
        Data_Watcher::instance()->clear();
        $this->model = Sfcms_Model::getModel('News');
        $this->obj   = $this->model->find(1);
    }

    public function testGetAlias()
    {
        $this->obj->name = 'Привет Мир!';
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

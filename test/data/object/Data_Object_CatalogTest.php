<?php
/**
 * Тест объекта каталога
 */
class Data_Object_CatalogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model_Catalog
     */
    public $model   = null;

    protected function setUp()
    {
        $this->model    =  app::getInstance()->getModel('Catalog');
    }


    public function testPath()
    {
        /**
         * @var Data_Object_Catalog $obj10
         */
        $obj10    = $this->model->createObject(
            array(
                'id'    => 10,
                'parent'=> 5,
                'name'  => 'obj10',
            )
        );
        $this->model->createObject(
            array(
                'id'    => 5,
                'parent'=> 1,
                'name'  => 'obj5',
            )
        );
        $this->model->createObject(
            array(
                'id'    => 1,
                'parent'=> 0,
                'name'  => 'obj1',
            )
        );
        $path = $obj10->path();
        $this->assertEquals('a:3:{i:0;a:2:{s:2:"id";i:1;s:4:"name";s:4:"obj1";}i:1;'
            .'a:2:{s:2:"id";i:5;s:4:"name";s:4:"obj5";}i:2;'
            .'a:2:{s:2:"id";i:10;s:4:"name";s:5:"obj10";}}', $path);
    }
}
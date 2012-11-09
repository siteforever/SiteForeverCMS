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
        Data_Watcher::instance()->clear();
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
                          . 'a:2:{s:2:"id";i:5;s:4:"name";s:4:"obj5";}i:2;'
                          . 'a:2:{s:2:"id";i:10;s:4:"name";s:5:"obj10";}}', $path);
    }


    public function testGetAlias()
    {
        /** @var $obj Data_Object_Catalog */
        $obj   = $this->model->createObject(
            array(
                'id'    => 1,
                'parent'=> 5,
                'name'  => 'Привет мир Yes!',
            )
        );
        $this->assertEquals('1-privet-mir-yes',$obj->getAlias());
    }


    public function testGetTitle()
    {
        $obj   = $this->model->createObject(
            array(
                'id'    => 1,
                'parent'=> 5,
                'name'  => 'Привет мир Yes!',
            )
        );
        $this->assertEquals('Привет мир Yes!', $obj->title);
        $obj->title = 'DEADBEEF!';
        $this->assertEquals('DEADBEEF!', $obj->title);
    }


    public function testGetUrl()
    {
        /** @var $modelPage Model_Page */
        $modelPage  = $this->model->getModel('Page');
        /** @var $page Data_Object_Page */
        $page   = $modelPage->createObject();
        $page->id   = 100500;
        $page->name = 'Электроника';
        $page->controller = 'catalog';
        $page->link = 100600;

        $modelPage->addToAll( $page );

        $category = $this->model->createObject(array(
            'id'    => 100600,
            'parent'=> 0,
            'cat'   => 1,
            'name'  => 'Электроника',
        ));

        $product = $this->model->createObject(array(
            'id'    => 100601,
            'parent'=> 100600,
            'cat'   => 0,
            'name'  => 'Samsung GT-P3110',
        ));

        $this->assertEquals('elektronika', $category->url);
        $this->assertEquals('elektronika/100601-samsung-gt-p3110', $product->url);
    }


    public function testGetPrice()
    {
        $product = $this->model->createObject(array(
            'id'    => 100601,
            'parent'=> 100600,
            'cat'   => 0,
            'name'  => 'Samsung GT-P3110',
            'price1'=> 1500, // розница
            'price2'=> 1000, // опт
        ));
        $this->assertEquals(1500, $product->price);
        $this->assertEquals(1000, $product->getPrice(true));
    }


    public function testGetMainImage()
    {
        // @todo написать тест
    }


    public function testGetImage()
    {
        // @todo написать тест
    }
}
<?php
/**
 * Тест объекта страницы
 */

class Data_Object_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Data_Object_Page
     */
    protected $page;

    public function setUp()
    {
    }

    public function testTest()
    {

    }
//    public function testGetAlias()
//    {
//        $this->page = Model::getModel('Page')->find(1);
//        $this->assertEquals('index', $this->page->getAlias());
//
//        $this->page = Model::getModel('Page')->find(3);
//        $this->assertEquals('supplies', $this->page->getAlias());
//    }

//    public function testCreateUrl()
//    {
//        $this->page = Model::getModel('Page')->find(1);
//        $this->assertEquals('/page/index/id/1', $this->page->createUrl());
//
//        $this->page = Model::getModel('Page')->find(3);
//        $this->assertEquals('/gallery/index/id/1', $this->page->createUrl());
//    }
}
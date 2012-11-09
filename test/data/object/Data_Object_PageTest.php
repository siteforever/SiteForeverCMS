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
        Data_Watcher::instance()->clear();
    }

    public function testTest()
    {

    }

    public function testGetAlias()
    {
        $this->page = Sfcms_Model::getModel('Page')->find(1);
        $this->assertEquals('index', $this->page->getAlias());

        $this->page = Sfcms_Model::getModel('Page')->find(3);
        $this->assertEquals('about', $this->page->getAlias());
    }

}
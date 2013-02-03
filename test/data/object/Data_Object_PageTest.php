<?php
/**
 * Тест объекта страницы
 */
use Module\Page\Object\Page;
use Sfcms\Data\Watcher;
use Sfcms\Model;

class Data_Object_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Page
     */
    protected $page;

    public function setUp()
    {
        Watcher::instance()->clear();
    }

    public function testTest()
    {

    }

    public function testGetAlias()
    {
        $this->page = Model::getModel('Page')->find(1);
        $this->assertEquals('index', $this->page->getAlias());

        $this->page = Model::getModel('Page')->find(3);
        $this->assertEquals('about', $this->page->getAlias());
    }

}
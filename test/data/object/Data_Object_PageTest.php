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
}

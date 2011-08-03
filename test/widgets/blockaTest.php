<?php

require_once 'widgets/block.a.php';
/**
 * Test class for View_Breadcrumbs.
 * Generated by PHPUnit on 2011-05-24 at 18:09:44.
 */
class Widgets_blockaTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testBasic()
    {
        $this->assertEquals(
            '<a href="/basic">Basic</a>',
            smarty_block_a(array('href'=>'basic'),'Basic')
        );
    }

    public function testBasicAnd2Class()
    {
        $this->assertEquals(
            '<a class="basic basic2" href="/basic">Basic</a>',
            smarty_block_a(array('href'=>'basic','class'=>array('basic','basic2')),'Basic')
        );
    }

    public function testControllerAndAction()
    {
        $this->assertEquals(
            '<a href="/page/edit/id/123">Правка</a>',
            smarty_block_a(array('controller'=>'page','action'=>'edit','id'=>'123'),'Правка')
        );
    }

    public function testController()
    {
        $this->assertEquals(
            '<a href="/page/index/id/123">Правка</a>',
            smarty_block_a(array('controller'=>'page','id'=>'123'),'Правка')
        );
    }

    public function testControllerAndClass()
    {
        $this->assertEquals(
            '<a class="hello" href="/page/index/id/123">Правка</a>',
            smarty_block_a(array('controller'=>'page','id'=>'123', 'class'=>'hello'),'Правка')
        );
    }

    public function testHrefAndController()
    {
        $this->assertEquals(
            '<a href="/about/contacts/controller=page/id=123">Правка</a>',
            smarty_block_a(array('href'=>'/about/contacts','controller'=>'page','id'=>'123'),'Правка')
        );
    }

    public function testHrefAndControllerAndClass()
    {
        $this->assertEquals(
            '<a class="hello" href="/about/contacts/controller=page/action=edit/id=123">Правка</a>',
            smarty_block_a(array('href'=>'/about/contacts','controller'=>'page','action'=>'edit','id'=>'123', 'class'=>'hello'),'Правка')
        );
    }
}

<?php
/**
 * @author keltanas <keltanas@gmail.com>
 */
use Module\Gallery\Controller\GalleryController;

class GalleryControllerTest extends \Sfcms\Test\WebCase
{
    /**
     * @var GalleryController
     */
    protected $controller;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->controller = new GalleryController($this->request);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testIndexAction()
    {

    }

    public function testAdminAction()
    {

    }

    public function testDeleteImageAction()
    {

    }

    public function testEditcatAction()
    {

    }

    public function testDelcatAction()
    {

    }

    public function testViewcatAction()
    {

    }

    public function testEditimgAction()
    {

    }

    public function testRealiasAction()
    {

    }

    public function testUpload()
    {
//        $this->object->execute();
//        $refl = new ReflectionMethod($this->object, 'assign');
//        $refl->setAccessible(true);
//        $retest = $refl->invoke($this->object,'test','testvalue');
//        $this->assertEquals('testvalue',$retest->textContent);
    }

}



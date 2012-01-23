<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 14.09.11
 * Time: 10:38
 * To change this template use File | Settings | File Templates.
 */
class GalleryControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Controller_Gallery
     */
    protected $object;

    /**
     * @var App
     */
    protected $app;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->app  = App::getInstance();
        $this->object = new Controller_Gallery( $this->app );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

//    public function testAccess()
//    {
//        $acs    = $this->object->access();
//        $this->assertEquals($acs['system'][1],'edit');
//        $this->assertEquals($acs['system'][0],'admin');
//        $this->assertEquals($acs['system'][4],'realias');
//    }

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



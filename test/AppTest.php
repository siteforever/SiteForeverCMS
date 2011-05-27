<?php
/**
 * Test class for App.
 * Generated by PHPUnit on 2011-02-04 at 15:02:34.
 */
class AppTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var App
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = App::getInstance();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testRun().
     */
    public function testRun()
    {
    }

    /**
     * @todo Implement testHandleRequest().
     */
    public function testHandleRequest()
    {
    }

    /**
     * @todo Implement testInvokeView().
     */
    public function testInvokeView()
    {
    }

    /**
     * @todo Implement testGetModel().
     */
    public function testGetModel()
    {
        $model  = $this->object->getModel('Structure');
        $this->assertTrue( $model instanceof Model_Structure, 'Model not correspond type' );
    }

    /**
     * @todo Implement testAutoload().
     */
    public function testAutoload()
    {
    }
}
?>

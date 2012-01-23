<?php
/**
 * Тест контроллера корзины
 */
 
class BasketControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Controller_Basket
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
//        $this->object = new Controller_Basket( $this->app );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testSome()
    {
        $this->assertEquals('10', '10');
    }

    public function testAddAction()
    {
        $_REQUEST['basket_prod_id']=1;
//        $this->object->addAction();
//        $this->assertRegExp('/.*/', App::getInstance()->getRequest()->getContent());
//        $this->assertEquals('', App::getInstance()->getRequest()->getContent());
    }

}

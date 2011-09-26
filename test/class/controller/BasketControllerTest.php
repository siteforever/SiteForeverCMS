<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 23.09.11
 * Time: 13:50
 * To change this template use File | Settings | File Templates.
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
        $this->object = new Controller_Basket( $this->app );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testAddAction()
    {
        $_REQUEST['basket_prod_id']=1;
        $this->object->addAction();

    }

}

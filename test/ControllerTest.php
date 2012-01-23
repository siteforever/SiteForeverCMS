<?php
/**
 * Проверка контроллера
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
class TestController extends Sfcms_Controller
{
    function indexAction()
    {
        return true;
    }
}

class ControllerTest extends PHPUnit_Framework_TestCase
{
    protected $obj;
    /**
     * @var Application_Abstract
     */
    protected $app;

    protected function setUp()
    {
        $this->app  = App::getInstance();
        $this->obj  = new TestController( $this->app );
    }

    public function testIndexAction()
    {
//        $this->assertTrue( $this->obj->indexAction() );
//        $this->assertEquals( $this->app->getRequest()->getTitle(), 'Главная' );
    }
}

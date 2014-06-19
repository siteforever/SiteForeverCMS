<?php
use Module\Market\Object\Order;
use Module\Market\Model\OrderModel;
use Sfcms\Model;
use Sfcms\Data\Watcher;

class Data_Object_OrderTest extends PHPUnit_Framework_TestCase
{
    /** @var OrderModel */
    protected $model;

    /** @var Order */
    protected $obj;

    protected function setUp()
    {
        /** @var OrderModel */
        $this->model = App::cms()->getModel('Order');

        $this->obj  = $this->model->createObject(array(
            'id'    => 100500,
            'date'  => time(),
            'email' => 'keltanas@gmail.com',
        ));
    }


    protected function tearDown()
    {
        Watcher::instance()->clear();
    }


    public function testValidateHash()
    {
        $code = md5($this->obj->id.':'.$this->obj->date.':'.$this->obj->email);
        $this->assertTrue( $this->obj->validateHash($code) );
        $this->assertFalse( $this->obj->validateHash(md5($this->obj->date)) );
    }


    public function testGetUrl()
    {
        $code = md5($this->obj->id.':'.$this->obj->date.':'.$this->obj->email);
        $this->assertEquals(
            '/order/view?id=100500&code='.$code,
            $this->obj->getUrl()
        );
    }
}

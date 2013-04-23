<?php
/**
 * Тест конфига
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
use Sfcms\Config;

class SfcmsConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    public $obj;

    protected function setUp()
    {
        $this->obj  = new Config(array(
            'sitename' => 'SiteForeverCMS',
        ));
        $this->obj->setDefault('db',array( 'login' => 'siteforever', ));
        $this->obj->setDefault('test',array(
            'foo1'  => 'foo1',
            'foo2'  => 'foo2',
        ));
    }

    public function testSet()
    {
        $this->obj->set('test', 'test');
        $this->assertEquals($this->obj->get('test'), 'test', 'Set() fail');
    }

    public function testGet()
    {
        $this->assertEquals($this->obj->get('sitename'), 'SiteForeverCMS', 'Get() failed');
        $this->assertEquals($this->obj->get('null value'), null, 'Undefined value not is null');
    }

    public function testGetI()
    {
        $this->assertEquals($this->obj->get('db.login'), 'siteforever', 'GetI() failed');
        $this->assertEquals($this->obj->get('db.null'), null, 'Undefined value not is null');
    }

    public function testSetI()
    {
        $this->obj->set('example.test.foo', 'siteforever');
        $this->assertEquals($this->obj->get('example.test.foo'), 'siteforever', 'Alias set fail');

        $this->obj->set('test.foo', 'siteforever');
        $this->assertEquals($this->obj->get('test.foo'), 'siteforever', 'Alias set fail');
    }

    public function testSetDefault()
    {
        $this->obj->setDefault('test', array(
            'foo1'  => 'fail',
            'foo2'  => 'fail',
            'foo3'  => 'foo3',
        ));

        $this->assertEquals($this->obj->get('test.foo1'), 'foo1', 'The default value should not be changed');
        $this->assertEquals($this->obj->get('test.foo2'), 'foo2', 'The default value should not be changed');
        $this->assertEquals($this->obj->get('test.foo3'), 'foo3', 'The default value should be set');
    }

    protected function tearDown()
    {
        unset( $this->obj );
    }
}

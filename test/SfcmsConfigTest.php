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
    public $config;

    protected function setUp()
    {
        $this->config  = new Config(SF_PATH . '/test/fixtures/config.php');
        $this->config->setDefault('db',array( 'login' => 'siteforever', ));
        $this->config->setDefault('test',array(
            'foo1'  => 'foo1',
            'foo2'  => 'foo2',
        ));
    }

    public function testSet()
    {
        $this->config->set('test', 'test');
        $this->assertEquals($this->config->get('test'), 'test', 'Set() fail');
    }

    public function testGet()
    {
        $this->assertEquals($this->config->get('sitename'), 'SiteForeverCMS', 'Get() failed');
        $this->assertNull($this->config->get('null value'), 'Undefined value not is null');
    }

    public function testGetI()
    {
        $this->assertEquals($this->config->get('db.login'), 'siteforever', 'GetI() failed');
        $this->assertEquals($this->config->get('db.null'), null, 'Undefined value not is null');
    }

    public function testSetI()
    {
        $this->config->set('example.test.foo', 'siteforever');
        $this->assertEquals($this->config->get('example.test.foo'), 'siteforever', 'Alias set fail');

        $this->config->set('test.foo', 'siteforever');
        $this->assertEquals($this->config->get('test.foo'), 'siteforever', 'Alias set fail');
    }

    public function testSetDefault()
    {
        $this->config->setDefault('test', array(
            'foo1'  => 'fail',
            'foo2'  => 'fail',
            'foo3'  => 'foo3',
        ));

        $this->assertEquals($this->config->get('test.foo1'), 'foo1', 'The default value should not be changed');
        $this->assertEquals($this->config->get('test.foo2'), 'foo2', 'The default value should not be changed');
        $this->assertEquals($this->config->get('test.foo3'), 'foo3', 'The default value should be set');
    }

    protected function tearDown()
    {
        unset( $this->config );
    }
}

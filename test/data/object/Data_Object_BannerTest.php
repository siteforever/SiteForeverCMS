<?php
/**
 * Тест объекта каталога
 */
class Data_Object_BannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model_Banner
     */
    public $model   = null;

    protected function setUp()
    {
        Data_Watcher::instance()->clear();
        $this->model    =  app::getInstance()->getModel('Banner');
        $_SERVER['HTTP_HOST'] = 'example.com';
    }


    public function testGetUrl()
    {
        $banner = $this->model->createObject(array(
            'name' => 'reklama',
            'url'  => 'http://reklama.com/test',
            'target' => '_parent',
        ));

        $this->assertEquals('http://reklama.com/test',$banner->url);

        $banner->url = '/test';
        $this->assertEquals('http://example.com/test',$banner->url);

        $_SERVER['HTTPS'] = true;
        $this->assertEquals('https://example.com/test',$banner->url);

        $_SERVER['HTTPS'] = 'off';
        $this->assertEquals('http://example.com/test',$banner->url);
    }


    public function testGetBlock()
    {
        $banner = $this->model->createObject(array(
            'name'  => 'reklama',
            'url'   => '/test',
            'target' => '_blank',
            'content' => '<img src="http://example.org/images/image.png">',
        ));

        $banner->id = 10;

        $this->assertEquals(
            '<a target="_blank" href="/banner/redirectbanner/id=10"><img src="http://example.org/images/image.png"></a>',
            $banner->block
        );

        $banner->id = null;

        try {
            $banner->block;
        } catch( Data_Exception $e ) {
            return true;
        }
        $this->fail('Expected exception');
    }
}
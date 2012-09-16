<?php
/**
 * Тест RSS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

class ControllerRssTest extends PHPUnit_Framework_TestCase
{
    protected $app = null;

    protected function setUp()
    {
        $this->app = App::getInstance();
    }

    public function testIndexAction()
    {
        $controller = new Controller_Rss();
        $result = $controller->indexAction();
        $this->assertInternalType('string', $result);
        $this->assertStringStartsWith(
            "<?xml version=\"1.0\"?>\n<rss version=\"2.0\"><channel><title>SiteForeverCMS</title>",
            $result
        );
    }

}

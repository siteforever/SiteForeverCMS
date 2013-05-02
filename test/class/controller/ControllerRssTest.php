<?php
/**
 * Тест RSS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
use Module\News\Controller\RssController;

class ControllerRssTest extends PHPUnit_Framework_TestCase
{
    protected $app = null;

    protected function setUp()
    {
        $this->app = App::getInstance();
    }

    public function testIndexAction()
    {
        $controller = new RssController();
        $result = $controller->indexAction();
        $this->assertInternalType('string', $result);
        $this->assertStringStartsWith(
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<rss version=\"2.0\">\n  <channel>\n    <title>SiteForeverCMS</title>",
            $result
        );
    }

}

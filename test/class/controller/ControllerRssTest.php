<?php
/**
 * Тест RSS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
use Module\News\Controller\RssController;

class ControllerRssTest extends \Sfcms\Test\WebCase
{
    public function testIndexAction()
    {
        $response = $this->runController('rss', 'index');
//        $controller = new RssController($this->request);
//        $result = $controller->indexAction();
//        $this->assertInternalType('string', $result);
        $this->assertStringStartsWith(
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<rss version=\"2.0\">\n  <channel>\n    <title>SiteForeverCMS</title>",
            $response->getContent()
        );
    }

}

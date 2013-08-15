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
        $crawler = new \Symfony\Component\DomCrawler\Crawler();
        $crawler->addHtmlContent($response->getContent());
        $this->assertEquals('SiteForeverCMS', $crawler->filter('rss>channel>title')->text());
    }
}

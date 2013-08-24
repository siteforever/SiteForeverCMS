<?php
/**
 * Тест RSS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Module\News\Test;

use Sfcms\Test\WebCase;

class ControllerRssTest extends WebCase
{
    public function testIndexAction()
    {
        $response = $this->runController('rss', 'index');
        $crawler = $this->createCrawler($response);
        $crawler->addHtmlContent($response->getContent());
        $this->assertEquals('SiteForeverCMS', $crawler->filter('rss>channel>title')->text());
    }
}

<?php
/**
 * Тестирование лога
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Test;

use Sfcms\Test\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class LogControllerTest extends TestCase
{
    public function testAdminAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runController('Log', 'admin');
        $crawler = new Crawler();
        $crawler->addHtmlContent($response->getContent());
        $this->assertEquals('Просмотр журнала изменений / SiteForeverCMS', $crawler->filter('title')->text());
        $this->assertEquals('Просмотр журнала изменений', $crawler->filter('h2')->text());
        $logList = $crawler->filter('#logs_list');
        $this->assertEquals(1, $logList->count());
        $this->assertEquals('sfcms-jqgrid', $logList->eq(0)->attr('class'));
        $this->assertEquals('jquery/jquery.jqGrid', $logList->eq(0)->attr('data-sfcms-module'));
        $this->assertInternalType('array', json_decode($logList->eq(0)->attr('data-sfcms-config'), true));
        $this->assertEquals(1, $crawler->filter('#logs_pager')->count());
    }
}

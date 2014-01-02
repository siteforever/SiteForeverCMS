<?php
/**
 * Тестирование лога
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Test;

use Sfcms\Test\WebCase;
use Symfony\Component\DomCrawler\Crawler;

class LogControllerTest extends WebCase
{
    public function testAdminAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runController('Log', 'admin');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Просмотр журнала изменений / SiteForeverCMS', $crawler->filterXPath('//title')->text());
        $this->assertEquals('Просмотр журнала изменений', $crawler->filterXPath('//h1')->text());
        $logList = $crawler->filterXPath('//table[@id="logs_list"]');
        $this->assertEquals(1, $logList->count());
        $this->assertEquals('sfcms-jqgrid', $logList->attr('class'));
        $this->assertEquals('jquery/jquery.jqGrid', $logList->attr('data-sfcms-module'));
        $this->assertInternalType('array', json_decode($logList->attr('data-sfcms-config'), true));
        $this->assertEquals(1, $crawler->filterXPath('//div[@id="logs_pager"]')->count());
    }
}

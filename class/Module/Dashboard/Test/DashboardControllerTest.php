<?php
/**
 * This file is part of the SiteForever package.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Dashboard\Test;


use Sfcms\Test\WebCase;

class DashboardControllerTest extends WebCase
{
    public function testIndexAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runRequest('/admin');
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Консоль', $crawler->filterXPath('//h1')->text());
    }
}

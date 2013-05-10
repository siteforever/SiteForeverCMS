<?php
/**
 * 
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Test;


class SitemapControllerTest extends \PHPUnit_Extensions_Selenium2TestCase
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://cms.sf');
    }

    public function testIndexAction()
    {
        $this->url('http://cms.sf/sitemap');
        $this->assertEquals('Карта сайта / SiteForeverCMS', $this->title());
        $this->assertEquals('Карта сайта', $this->byCssSelector('h1')->text());
        $this->assertGreaterThan(0,count(explode("\n", $this->byCssSelector('.sfcms-sitemap')->text())));
    }

}
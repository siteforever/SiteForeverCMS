<?php
/**
 * Тестирование поискового контроллера
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Search\Test;


use Module\Search\Controller\SearchController;
use Sfcms\Request;
use Sfcms\Test\TestCase;

class SearchControllerTest extends TestCase
{
    /** @var SearchController */
    public $controller;

    /** @var Request */
    public $request;

    public function testIndexAction()
    {
        \App::getInstance()->getTpl()->assign('error');
        $this->request->query->set('query', null);
        $response = $this->runController('search');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Поиск', $crawler->filterXPath('//h1')->text());
        $this->assertEquals(1, $crawler->filterXPath('//form')->count());

        \App::getInstance()->getTpl()->assign('error');
        $this->request->query->set('query', 'ab');
        $response = $this->runController('search');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Поисковая фраза слишком короткая', $crawler->filterXPath('//div[@class="alert alert-error"]')->text());

        \App::getInstance()->getTpl()->assign('error');
        $this->request->query->set('query', 'страница');
        $response = $this->runController('search');
        $crawler = $this->createCrawler($response);
        $this->assertEquals(10, $crawler->filterXPath('//h4')->count());
        $this->assertEquals(2, $crawler->filterXPath('//div[@class="paging"]/a')->count());
    }

}

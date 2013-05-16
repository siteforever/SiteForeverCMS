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

    protected function setUp()
    {
        parent::setUp();
        $this->controller = new SearchController($this->request);
    }

    public function testIndexAction()
    {
        $this->request->query->set('query', null);
        $response = $this->controller->indexAction();
        $this->assertNull($response['query']);

        $this->request->query->set('query', 'ab');
        $response = $this->controller->indexAction();
        $this->assertEquals($response['error'], 'Поисковая фраза слишком короткая');

        $this->request->query->set('query', 'страница');
        $response = $this->controller->indexAction();
        $this->arrayHasKey('search', $response);
        $this->assertInstanceOf('Sfcms\Data\Collection', $response['result']);
        $this->assertEquals(18, $response['paging']->count);
        $this->assertEquals('/search/query=страница/page=2', $response['paging']->next);
    }

}

<?php
/**
 * Тестирование поискового контроллера
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Search\Test;


use Module\Search\Controller\SearchController;

class SearchControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var SearchController */
    public $controller;

    protected function setUp()
    {
        $this->controller = new SearchController();
    }

    public function testIndexAction()
    {
        $response = $this->controller->indexAction();
        $this->assertEquals($response['error'], 'Поисковая фраза слишком короткая');
        \App::getInstance()->getRequest()->query->set('query', 'страница');
        $response = $this->controller->indexAction();
        $this->arrayHasKey('search', $response);
        $this->arrayHasKey('result', $response);
        $this->assertEquals(6, $response['result']->count());
    }

}
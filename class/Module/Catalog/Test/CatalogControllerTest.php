<?php
/**
 * Тестирует контроллер каталога
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Test;

use Sfcms\Test\WebCase;

class CatalogControllerTest extends WebCase
{
    /**
     * @covers \Module\Catalog\Controller\CatalogController::indexAction
     * @covers \Module\Catalog\Controller\CatalogController::viewCategory
     * @covers \Sfcms\Model::getRelation
     */
    public function testViewCategory()
    {
        $response = $this->runRequest('/catalog');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Каталог', $crawler->filter('h1')->text());

        $response = $this->runRequest('/catalog', 'GET', array('order'=>'name'));
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Каталог', $crawler->filter('h1')->text());

        $this->runRequest('/catalog', 'GET', array('order'=>'bad'));
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Каталог', $crawler->filter('h1')->text());
    }

    /**
     * @covers \Module\Catalog\Controller\CatalogController::indexAction
     * @covers \Module\Catalog\Controller\CatalogController::viewProduct
     */
    public function testViewProduct()
    {
        $response = $this->runRequest('/catalog/telefony/telefony-htc/7-htc-evo-3d');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('HTC Evo 3D', $crawler->filter('h1')->text());
    }

    /**
     * @covers \Module\Page\Controller\PageController::saveAction
     * @covers \Module\Page\Controller\PageController::init
     * @covers \App::run
     * @covers \App::handleRequest
     * @covers \Sfcms\Kernel\AbstractKernel::getResolver
     * @covers \Sfcms\Controller\Resolver::dispatch
     */
    public function testPageSaveAction()
    {
        $this->session->set('user_id', 1);
        $_POST = array('structure' => array(
            'parent' => 1,
            'name' => 'Test Category',
            'template' => 'inner',
            'alias' => 'test-category',
            'controller' => 'catalog',
            'action' => 'index',
        ));
        $this->request->setAjax(true, 'json');
        $response = $this->runController('page', 'save');
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent());
        $this->assertEquals(0, $json->error);
        $this->assertEquals("Данные сохранены успешно", $json->msg);
    }
}

<?php
/**
 * Тестирует контроллер каталога
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Test;

use Sfcms\Test\WebCase;

class CatalogControllerTest extends WebCase
{

    public function testIndexAction()
    {
//        $response = $this->runRequest('/catalog/velosipedy');
//        var_dump($response->getContent());
    }

    /**
     * @covers \Module\Page\Controller\PageController::saveAction
     * @covers \Module\Page\Controller\PageController::init
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

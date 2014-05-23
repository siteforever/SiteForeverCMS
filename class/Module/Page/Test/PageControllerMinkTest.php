<?php
/**
 * Pages test
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Test;

use Sfcms\Test\WebCase;

class PageControllerMinkTest extends WebCase
{
    public function testHome()
    {
        $this->visitPage('/');
        $this->assertEquals('Главная', $this->getPage()->find('css', 'h1')->getHtml());
    }

    public function testDeleteAction()
    {
        $this->markTestSkipped();
        $this->loginAsAdmin();

        $this->request->setAjax(true, 'json');
        $this->request->query->set('id', 62);
        $response = $this->runController('page', 'delete');
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent());
        $this->assertEquals(0, $json->error);
        $this->assertEquals('ok', $json->msg);
        $this->assertEquals(62, $json->id);
    }

    public function testAdminAction()
    {
        $this->loginAsAdmin();
        $this->visitPage('/page/admin');
        $this->assertEquals('Структура сайта', $this->findCss('h1')->getText());
    }

    public function testCreateAction()
    {
        $this->markTestSkipped();

        $parentId = 1;

        $this->loginAsAdmin();
        $response = $this->runXhrRequest('/page/create?id='.$parentId, 'POST');
        $crawler = $this->createCrawler($response);
        $addUrl = $crawler->filter('#url')->attr('value');
        $this->assertEquals('/page/add', $addUrl);
        $this->assertEquals(1, $crawler->filter('#module')->count());
        $this->assertEquals('1', $crawler->filter('#id')->attr('value'));

        $_POST = array(
            'module' => 'news',
            'name' => 'Новости',
            'parent' => $parentId,
        );
        $response = $this->runXhrRequest($addUrl, 'POST', $_POST);
//        print $response->getContent();
        $crawler = $this->createCrawler($response);
        $this->assertEquals('form_structure', $crawler->filter('#form_structure')->attr('name'));
        $this->assertEquals($_POST['name'], $crawler->filter('#structure_name')->attr('value'));
        $this->assertEquals($parentId, $crawler->filter('#structure_parent option[selected="selected"]')->attr('value'));
        $this->assertEquals($_POST['module'], $crawler->filter('#structure_controller option[selected="selected"]')->attr('value'));
    }

    public function testShowHide()
    {
        $this->markTestSkipped();

        $this->loginAsAdmin();
        $response = $this->runXhrRequest('/page/hidden?id=3');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Выкл', trim($crawler->filter('a')->text()));
        $this->assertEquals('sfcms-icon sfcms-icon-lightbulb-off', $crawler->filter('i')->attr('class'));

        $response = $this->runXhrRequest('/page/hidden?id=3');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Вкл', trim($crawler->filter('a')->text()));
        $this->assertEquals('sfcms-icon sfcms-icon-lightbulb', $crawler->filter('i')->attr('class'));
    }
}

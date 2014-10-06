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
        $this->markTestSkipped();

        $this->visitPage('/');
        $h1 = $this->getPage()->find('css', 'h1');
        $this->assertNotNull($h1);
        $this->assertEquals('Главная', $h1->getHtml());
    }

    public function testDeleteAction()
    {
        $this->markTestSkipped();

        $this->loginAsAdmin();
        $this->visitPage('/page/admin');
        $this->assertEquals('Удалить', $this->getTextByCss('#item62 .do_delete'));
        $this->findCss('#item62 .do_delete')->click();
        $this->getSession()->wait(500);
        $this->getPage()->pressButton('Ok');
        $this->getSession()->wait(1000);
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
        $this->visitPage('/page/admin');
        $this->assertEquals("Вкл", $this->getTextByCss('#item3 .order_hidden'));
        $this->findCss('#item3 .order_hidden')->click();
        $this->getSession()->wait(1000, '$("#item3 .order_hidden").text() == "Выкл"');
        $this->assertEquals("Выкл", $this->getTextByCss('#item3 .order_hidden'));
        $this->findCss('#item3 .order_hidden')->click();
        $this->getSession()->wait(1000, '$("#item3 .order_hidden").text() == "Вкл"');
        $this->assertEquals("Вкл", $this->getTextByCss('#item3 .order_hidden'));
    }
}

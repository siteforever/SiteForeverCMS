<?php
/**
 * @author KelTanas
 */

namespace Module\News\Test;


use Sfcms\Test\WebCase;

class ControllerNewsTest extends WebCase
{
    public function testAdminAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runRequest('/news/admin');
        $crawler = $this->createCrawler($response);

        $this->assertEquals('Новости', $crawler->filter('#workspace h1')->text());
        $this->assertEquals(1, $crawler->filter('#workspace table.table')->count());

        $this->assertEquals('/news/list?id=1', $crawler->selectLink('Новости')->attr('href'));
        $response = $this->click($crawler->selectLink('Новости'));

        $listCrawler = $this->createCrawler($response);
        $listCount = $listCrawler->filter('table tr')->count();
        $this->assertEquals(6, $listCount);

        $this->assertEquals('/news/edit?cat=1', $listCrawler->selectLink('Создать статью')->attr('href'));
        $response = $this->click($listCrawler->selectLink('Создать статью'), 'GET', true);

        $crawler = $this->createCrawler($response);
        $this->assertEquals('form_news', $crawler->filter('form')->attr('id'));
        $this->assertEquals('/news/edit', $crawler->filter('form')->attr('action'));

        $_POST = array(
            'news' => array(
                'id' => '',
                'author_id' => $this->session->get('user_id'),
                'name' => 'test',
                'cat_id' => '1',
                'date' => date('d.m.Y'),
                'main' => '0',
                'priority' => '0',
                'image' => '',
                'title' => '',
                'keywords' => '',
                'description' => '',
                'notice' => '',
                'text' => '',
                'hidden' => '0',
                'protected' => '0',
                'deleted' => '0',
            ),
        );
        $jsonXhrServer = array_merge($this->serverAjax, $this->serverJson);
        $response = $this->runRequest('/news/edit', 'POST', $_POST, array(), array(), $jsonXhrServer);
        $response = json_decode($response->getContent());
        $this->assertEquals('0', $response->error);
        $this->assertEquals('Данные сохранены успешно', $response->msg);

        $response = $this->runRequest('/news/list?id=1', 'GET', array(), array(), array(), $this->serverAjax);
        $listCrawler = $this->createCrawler($response);
        $listCount = $listCrawler->filter('table tr')->count();
        $this->assertEquals(7, $listCount);
    }
}

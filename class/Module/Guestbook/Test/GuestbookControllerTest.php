<?php
/**
 * Testing guestbook controller
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Guestbook\Test;

use Sfcms\Test\WebCase;

class GuestbookControllerTest extends WebCase
{
    public function testIndexAction()
    {
        $response = $this->runRequest('/guest');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Гостевая / SiteForeverCMS', $crawler->filterXPath('//title')->text());
        $this->assertEquals('Гостевая', $crawler->filterXPath('//h1')->text());
        $form = $crawler->filterXPath('//form[@id="form_guestbook"]');
        $this->assertEquals(1, $form->count());
        $this->assertEquals('form_guestbook', $form->attr('name'));
        $this->assertEquals(4, $form->filterXPath('//input')->count());
//        print $response->getContent();
    }

    public function testAdminAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runRequest('/guestbook/admin');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Модуль гостевой / SiteForeverCMS', $crawler->filterXPath('//title')->text());
        $this->assertEquals('Модуль гостевой', $crawler->filterXPath('//h1')->text());
        $this->assertEquals(8, $crawler->filterXPath('//div[@id="workspace"]/table//tr')->count());
//        print $response->getContent();
    }

    public function testEditAction()
    {
        $this->session->set('user_id', 1);
        $response = $this->runRequest('/guestbook/edit?id=7');
        $crawler = $this->createCrawler($response);
        $form = $crawler->filterXPath('//form')->first();
        $this->assertEquals('form_guestbook_edit', $form->attr('id'));
        $this->assertEquals('form_guestbook_edit', $form->attr('name'));
        $this->assertEquals('/guestbook/edit', $form->attr('action'));
//        print $response->getContent();
    }
}

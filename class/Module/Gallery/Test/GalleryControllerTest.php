<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Gallery\Test;


use Sfcms\Test\WebCase;

class GalleryControllerTest extends WebCase
{
    public function testIndexAction()
    {
        $response = $this->runRequest('/portfolio');
        print $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Портфолио', $crawler->filterXPath('//h1')->text());
        $this->assertEquals(3, $crawler->filterXPath('//ul[@class="gallery_list"]/li')->count());

        $response = $this->click($crawler->selectLink('Пейзаж'));
        print $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Пейзаж', $crawler->filterXPath('//h1')->text());
        $this->assertEquals('/portfolio/belyi-cvetok', $crawler->selectLink('Белый цветок »')->attr('href'));

        $response = $this->runRequest('/pictures');
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Картинки', $crawler->filterXPath('//h1')->text());
        $this->assertEquals(5, $crawler->filterXPath('//ul[@class="gallery_list"]/li')->count());
    }

    public function testAdminAction()
    {
        // loading main admin page
        $this->session->set('user_id', 1);
        $response = $this->runRequest('/gallery/admin');
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Галерея изображений', $crawler->filterXPath('//h2')->text());

        // loading section content
        $response = $this->click($crawler->selectLink('Портфолио'));
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Портфолио / SiteForeverCMS', $crawler->filterXPath('//title')->text());
        $this->assertEquals('Портфолио', $crawler->filterXPath('//h2')->text());
        $this->assertEquals(3, $crawler->filterXPath('//ul[@id="gallery"]/li')->count());
        $form = $crawler->filterXPath('//form[@id="load_images"]');
        $this->assertEquals(1, $form->count());
        $this->assertEquals(2, $form->filterXPath('//input')->count());

        // loading edit form
        $responseEdit = $this->click($crawler->filterXPath('//a[@class="gallery_picture_edit"]')->first());
        $crawlerEdit = $this->createCrawler($responseEdit);
        $form = $crawlerEdit->filterXPath('//form[@id="form_gallery_picture"]');
        $this->assertEquals(1, $form->count());
        $this->assertEquals(3, $form->filterXPath('//input')->count());
        $this->assertEquals(1, $form->filterXPath('//textarea')->count());

        // testing switch on/off on image
        $responseSwitch = $this->click($crawler->filterXPath('//a[@class="gallery_picture_switch"]')->first());
        $json = json_decode($responseSwitch->getContent());
        $this->assertEquals(25, $json->id);
        $this->assertEquals(0, $json->error);
        $this->assertEquals('', $json->msg);
        $this->assertEquals("<i class='sfcms-icon sfcms-icon-lightbulb-off' title='Вкл'></i>", $json->img);
        $responseSwitch = $this->click($crawler->filterXPath('//a[@class="gallery_picture_switch"]')->first());
        $json = json_decode($responseSwitch->getContent());
        $this->assertEquals("<i class='sfcms-icon sfcms-icon-lightbulb' title='Выкл'></i>", $json->img);
    }
}

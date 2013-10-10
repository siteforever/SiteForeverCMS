<?php
/**
 * Тест контроллера корзины
 */
namespace Module\Market\Test;

use Sfcms\Test\WebCase;
use Symfony\Component\DomCrawler\Crawler;

class BasketControllerTest extends WebCase
{
    public function testAddAction()
    {
        $this->request->request->set('basket_prod_id', 8);
        $this->request->request->set('basket_prod_count', 1);
        $this->request->request->set('basket_prod_price', '2000000');
        $response = $this->runController('basket', 'add');

        $json = @json_decode($response->getContent(), 1);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayNotHasKey('id', $json);
        $this->assertEquals(1, $json['count']);
        $this->assertEquals('2000000,00', $json['sum']);
        $this->assertArrayHasKey('widget', $json);

        $response = $this->runController('basket', 'add');
        $json = @json_decode($response->getContent(), 1);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayNotHasKey('id', $json);
        $this->assertEquals(2, $json['count']);
        $this->assertEquals('4000000,00', $json['sum']);
    }

    public function testCountAction()
    {
        // set count for unbasket product
        $this->request->set('id', 8);
        $this->request->set('count', 4);
        $response = $this->runController('basket', 'count');

        $json = @json_decode($response->getContent(), 1);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertEquals('8', $json['id']);
        $this->assertArrayHasKey('error', $json);

        // add new products
        $this->request->request->set('basket_prod_id', 7);
        $this->request->request->set('basket_prod_count', 4);
        $this->request->request->set('basket_prod_price', '15000');
        $this->runController('basket', 'add');

        $this->request->request->set('basket_prod_id', 10);
        $this->request->request->set('basket_prod_count', 2);
        $this->request->request->set('basket_prod_price', '18000');
        $this->runController('basket', 'add');

        $this->request->set('id', 10);
        $this->request->set('count', 4);
        $response = $this->runController('basket', 'count');

        $this->request->set('id', 7);
        $this->request->set('count', 3);
        $response = $this->runController('basket', 'count');

        $json = @json_decode($response->getContent(), 1);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayNotHasKey('id', $json);
        $this->assertArrayNotHasKey('error', $json);
        $this->assertEquals(7, $json['count']);
        $this->assertEquals('117000,00', $json['sum']);
        $this->assertArrayHasKey('widget', $json);
        $widget = new Crawler();
        $widget->addHtmlContent($json['widget']);
        $this->assertEquals('7 шт.', $widget->filter('.b-basket-info tr:nth-child(1) td:nth-child(2)')->text());
        $this->assertEquals('117000,00 руб.', $widget->filter('.b-basket-info tr:nth-child(2) td:nth-child(2)')->text());
    }


    public function testDeleteAction()
    {
        // delete unbasket product
        $this->request->set('key', 0);
        $this->request->set('count', 4);
        $this->request->headers->set('content-type', 'application/json');
        $this->request->headers->set('accept', 'application/json');
        $response = $this->runController('basket', 'delete');

        $json = @json_decode($response->getContent(), 1);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertEquals(1, $json['error']);
        $this->assertEquals("Item with key 0 not found", $json['msg']);

        // add new product
        $this->request->request->set('basket_prod_id', 8);
        $this->request->request->set('basket_prod_count', 10);
        $this->request->request->set('basket_prod_price', '2000000');
        $this->runController('basket', 'add');

        // delete this product
        $this->request->set('key', 0);
        $this->request->set('count', 4);
        $this->request->headers->set('accept', 'application/json');
        $response = $this->runController('basket', 'delete');

        $json = @json_decode($response->getContent(), 1);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayNotHasKey('id', $json);
        $this->assertEquals(6, $json['count']);
        $this->assertEquals(number_format(6*2000000, 2, ",", ""), $json['sum']);
        $this->assertArrayHasKey('widget', $json);
    }


    public function testCreateOrder()
    {
        $response = $this->runRequest('/basket');
        $crawler = $this->createCrawler($response);
        $this->assertEquals('В корзине нет товаров', $crawler->filter('div.b-content p')->text());

        $this->request->getBasket()->add(7, null, 1, 15000);
        $this->request->getBasket()->add(10, null, 2, 18000);

        $response = $this->runController('basket');
        $crawler = $this->createCrawler($response);
        $form = $crawler->filter('form#form_order');
        $this->assertEquals(1, $form->count());
        $this->assertEquals(2, $form->filter('table.table tbody tr')->count());
    }
}

<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Market\Test;


use Sfcms\Test\WebCase;

class OrderControllerTest extends WebCase
{
    public function testOrderAdmin()
    {
        $this->markTestSkipped();
        $response = $this->runRequest('/order/admin');
        $this->assertEquals(302, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('1;url=/user/login', $crawler->filter('meta[http-equiv="refresh"]')->attr('content'));

        $this->session->set('user_id', 1);
        $response = $this->runRequest('/order/admin');
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Заказы', $crawler->filter('h1')->html());
        $this->assertEquals('/order/admin', $crawler->filter('#workspace>form')->attr('action'));
        $this->assertEquals('1', $crawler->filter('#workspace>table')->count());
        $this->assertEquals('1', $crawler->filter('#workspace>div.pagination')->count());

        $response = $this->runRequest('/order/admin', 'GET', array('id'=>1));
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Контактные данные', $crawler->filter('h4')->eq(0)->html());
        $this->assertEquals('Доставка', $crawler->filter('h4')->eq(1)->html());
        $this->assertEquals('Позиции:', $crawler->filter('h4')->eq(2)->html());
        $this->assertEquals('Итого к оплате: 3400', $crawler->filter('h4')->eq(3)->html());
//        $this->assertEquals('Заказ <b>№ 1</b> от 12.09.2012 (02:16)', $crawler->filter('h1')->html());

        $response = $this->runRequest('/order/admin', 'GET', array('user'=>'admin@ermin.ru'));
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals(3, $crawler->filter('a.filterEmail')->count());
    }

    public function testOrderIndex()
    {
        $response = $this->runRequest('/order');
        $this->assertEquals(302, $response->getStatusCode(), $response->getContent());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('1;url=/user/login', $crawler->filter('meta[http-equiv="refresh"]')->attr('content'));

        $this->session->set('user_id', 1);
        $response = $this->runRequest('/order');
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Мои заказы', $crawler->filter('h1')->html());
        $this->assertEquals(0, $crawler->filter('.error')->count());
    }

    public function testOrderView()
    {
        $response = $this->runRequest('/order/view', 'GET', array('id'=>1));
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->runRequest('/order/view', 'GET', array('id'=>1, 'code'=>'62554daebec73aa3c0850b320ecac5ee'));
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('Николай Ермин, Ваш заказ №1', $crawler->filter('h1')->html());
    }

    public function testOrderStatus()
    {
        $response = $this->runRequest('/order/status', 'POST', array('id'=>1));
        $this->assertEquals(302, $response->getStatusCode());
        $crawler = $this->createCrawler($response);
        $this->assertEquals('1;url=/user/login', $crawler->filter('meta[http-equiv="refresh"]')->attr('content'));

        $this->session->set('user_id', 1);
        $response = $this->runRequest('/order/status?id=1', 'POST', array());
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent(), true);
        $this->assertEquals('1', $json['error']);
        $this->assertEquals('Status not defined', $json['msg']);

        $response = $this->runRequest('/order/status?id=1', 'POST', array('new_status'=>2));
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent(), true);
        $this->assertEquals('2', $json['status']);
        $this->assertEquals('Сохранено успешно', $json['msg']);

        $this->setExpectedException('Symfony\Component\Routing\Exception\MethodNotAllowedException');
        $this->runRequest('/order/status', 'GET', array('id'=>1));
    }
}

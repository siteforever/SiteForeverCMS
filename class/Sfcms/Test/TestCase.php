<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Test;

use PHPUnit_Framework_TestCase;
use Sfcms\Request;
use Sfcms\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\DomCrawler\Crawler;

class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var Request */
    protected $request;

    /** @var Session */
    protected $session;

    /** @var Router */
    protected $router;

    protected function setUp()
    {
        $this->request = Request::create('/');
        $this->session = new Session(new MockArraySessionStorage());
        $this->session->set('user_id', null);
        $this->request->setSession($this->session);
        $this->session->start();
        \App::getInstance()->getContainer()->set('request', $this->request);
        \App::getInstance()->getRouter()->setRequest($this->request);
        \App::getInstance()->getAuth()->setRequest($this->request);
    }

    protected function createCrawler(Response $response)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($response->getContent());
        return $crawler;
    }

    /**
     * @param        $controller
     * @param string $action
     *
     * @return Response
     */
    protected function runController($controller, $action = 'index')
    {
        $this->request->clearFeedback();
        $this->request->setController($controller);
        $this->request->setAction($action);
        return \App::getInstance()->handleRequest($this->request);
    }

    /**
     * @param        $uri
     * @param string $method
     * @param array  $parameters
     * @param array  $cookies
     * @param array  $files
     * @param array  $server
     * @param null   $content
     *
     * @return Response
     */
    protected function runRequest($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        $this->request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
        $this->request->query->set('route', $uri);
        $this->request->setSession($this->session);
        return \App::getInstance()->handleRequest($this->request);
    }


    protected function followRedirect(RedirectResponse $response)
    {
        $this->assertTrue($response->isRedirection());
        return $this->runRequest($response->getTargetUrl());
    }
}

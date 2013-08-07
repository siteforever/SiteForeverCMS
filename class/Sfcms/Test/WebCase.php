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

class WebCase extends PHPUnit_Framework_TestCase
{
    /** @var Request */
    protected $request;

    /** @var Session */
    protected $session;

    /** @var Router */
    protected $router;

    protected function setUp()
    {
        $_POST = array();
        $_GET = array();
        $_FILES = array();
        $this->request = Request::create('/');
        $this->session = new Session(new MockArraySessionStorage());
        $this->session->set('user_id', null);
        $this->request->setSession($this->session);
        $this->session->start();
        \App::cms()->getContainer()->set('request', $this->request);
        \App::cms()->getRouter()->setRequest($this->request);
        \App::cms()->getAuth()->setRequest($this->request);
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
        $_GET && $this->request->query->replace($_GET);
        $_POST && $this->request->request->replace($_POST);
        return \App::cms()->handleRequest($this->request);
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
        return \App::cms()->handleRequest($this->request);
    }

    /**
     * Click by link and get response
     * @param Crawler $crawlerLink
     *
     * @return null|Response
     */
    protected function click(Crawler $crawlerLink)
    {
        return $this->runRequest($crawlerLink->attr('href'));
    }

    /**
     * Following to redirect header
     * @param RedirectResponse $response
     *
     * @return Response
     */
    protected function followRedirect(RedirectResponse $response)
    {
        $this->assertTrue($response->isRedirection());
        return $this->runRequest($response->getTargetUrl());
    }
}

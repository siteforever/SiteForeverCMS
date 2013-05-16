<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Test;

use PHPUnit_Framework_TestCase;
use Sfcms\Request;
use Sfcms\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

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
        $this->request->setSession($this->session);
        $this->session->start();
        $this->router  = new Router($this->request);
        \App::getInstance()->setRouter($this->router);
    }

    /**
     * @param        $controller
     * @param string $action
     *
     * @return Response
     */
    protected function runController($controller, $action = 'index')
    {
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
        $this->request->setSession($this->session);
        return \App::getInstance()->handleRequest($this->request);
    }
}

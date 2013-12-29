<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Event;


use Sfcms\Request;
use Sfcms\Router;
use Symfony\Component\EventDispatcher\Event;

class RouteEvent extends Event
{
    const ROUTER_ROUTE = 'router.route';

    /** @var string */
    private $route;

    /** @var Router */
    private $router;

    /** @var Request */
    private $request;

    /** @var mixed */
    private $routed = false;

    function __construct($route, Request $request, Router $router)
    {
        $this->route = $route;
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @return \Sfcms\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Sfcms\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param mixed $routed
     */
    public function setRouted($routed)
    {
        $this->routed = $routed;
    }

    /**
     * @return mixed
     */
    public function getRouted()
    {
        return $this->routed;
    }

    /**
     * @return boolean
     */
    public function isRouted()
    {
        return (bool) $this->routed;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }
}

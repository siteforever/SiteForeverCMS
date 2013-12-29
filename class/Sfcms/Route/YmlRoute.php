<?php
/**
 * Routing by defined routes
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Route;

use Module\System\Event\RouteEvent;
use Sfcms\Request;
use Sfcms\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

class YmlRoute extends Route
{
    public function route(RouteEvent $event)
    {
        $router = $this->getSymfonyRouter($event->getRequest());
        try {
            $match = $router->match($event->getRoute());
            foreach ($match as $param => $value) {
                $event->getRequest()->set($param, $value);
            }
            $event->setRouted(true);
            $event->stopPropagation();
        } catch (ResourceNotFoundException $e) {
        }
    }
}

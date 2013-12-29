<?php
/**
 * Default route regulations
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Route;

use Module\System\Event\RouteEvent;
use Sfcms\Request;
use Sfcms\Route;

class DefaultRoute extends Route
{
    public function route(RouteEvent $event)
    {
        $routePieces = explode('/', $event->getRoute());

        if (count($routePieces) == 1) {
            $event->setRouted(array(
                'controller' => $routePieces[0],
                'action'     => 'index',
            ));
            $event->stopPropagation();
        } elseif (count($routePieces) > 1) {
            $event->setRouted(array(
                'controller' => $routePieces[0],
                'action'     => $routePieces[1],
                'params'     => $this->extractAsParams(array_slice($routePieces, 2)),
            ));
            $event->stopPropagation();
        }
    }

}

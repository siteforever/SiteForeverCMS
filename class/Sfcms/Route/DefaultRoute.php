<?php
/**
 * Default route regulations
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Route;

use Sfcms\Request;
use Sfcms\Route;

class DefaultRoute extends Route
{
    public function route(Request $request, $route)
    {
        $routePieces = explode('/', $route);

        if (count($routePieces) == 1) {
            return array(
                'controller' => $routePieces[0],
                'action'     => 'index',
            );
        } elseif (count($routePieces) > 1) {
            return array(
                'controller' => $routePieces[0],
                'action'     => $routePieces[1],
                'params'     => $this->extractAsParams(array_slice($routePieces, 2)),
            );
        }

        return false;
    }

}

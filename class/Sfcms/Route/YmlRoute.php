<?php
/**
 * Routing by defined routes
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Route;

use Sfcms\Request;
use Sfcms\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

class YmlRoute extends Route
{
    /**
     *
     * @param $request
     * @param $route
     *
     * @return mixed
     */
    public function route(Request $request, $route)
    {
        $router = $this->getRouter($request);
        try {
            $match = $router->match($route);
            foreach ($match as $param => $value) {
                $request->set($param, $value);
            }
        } catch (ResourceNotFoundException $e) {
            return false;
        }

        return true;
    }

}

<?php
/**
 * Find route by XML config
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Route;

use Sfcms\Route;

class XmlRoute extends Route
{
    /**
     * @param $route
     *
     * @return mixed
     */
    public function route($route)
    {
        $xml_routes_file = realpath(__DIR__.'/../../../protected/routes.xml');
        if (file_exists($xml_routes_file)) {
            $xmlRoutes = new \SimpleXMLIterator(file_get_contents($xml_routes_file));
            if ($xmlRoutes) {
                foreach ($xmlRoutes as $XMLRoute) {
                    $regexp = '@^' . str_replace('*', '([\/\w-]*)', $XMLRoute['alias']) . '@ui';
                    if ($XMLRoute['active'] !== "0" && preg_match($regexp, $route, $match)) {
                        $controller = (string)$XMLRoute->controller;
                        $action     = isset($XMLRoute->action) ? (string)$XMLRoute->action : 'index';
                        $protected  = $XMLRoute['protected'];
                        $system     = $XMLRoute['system'];
                        if (isset($match[1])) {
                            $this->extractAsParams(explode('/', $match[1]));
                        }

                        return array(
                            'controller' => $controller,
                            'action'     => $action,
                            'params'     => array(
                                'protected' => $protected,
                                'system'    => $system,
                            ),
                        );
                    }
                }
            }
        }

        return false;
    }

}
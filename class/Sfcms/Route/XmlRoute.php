<?php
/**
 * Find route by XML config
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Route;

use Module\System\Event\RouteEvent;
use Sfcms\Route;

class XmlRoute extends Route
{
    public function route(RouteEvent $event)
    {
        $xml_routes_file = realpath(__DIR__.'/../../../protected/routes.xml');
        if (file_exists($xml_routes_file)) {
            $xmlRoutes = new \SimpleXMLIterator(file_get_contents($xml_routes_file));
            if ($xmlRoutes) {
                foreach ($xmlRoutes as $XMLRoute) {
                    $regexp = '@^' . str_replace(array('/','*'), array('\/','([^\/]*)'), $XMLRoute['alias']) . '@ui';
                    if ($XMLRoute['active'] !== "0" && preg_match($regexp, $event->getRoute(), $match)) {
                        $controller = (string)$XMLRoute->controller;
                        $action     = isset($XMLRoute->action) ? (string)$XMLRoute->action : 'index';
                        $protected  = $XMLRoute['protected'];
                        $system     = $XMLRoute['system'];
                        if (isset($match[1])) {
                            $this->extractAsParams(explode('/', $match[1]));
                        }

                        $event->setRouted(array(
                            'controller' => $controller,
                            'action'     => $action,
                            'params'     => array(
                                'protected' => $protected,
                                'system'    => $system,
                            ),
                        ));
                        $event->stopPropagation();
                    }
                }
            }
        }
    }

}

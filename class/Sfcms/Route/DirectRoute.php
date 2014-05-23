<?php
/**
 * Direct Route
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;

use Module\System\Event\RouteEvent;
use Sfcms\Request;
use Sfcms\Route;
use ReflectionClass;
use App;

class DirectRoute extends Route
{
    /** @param array */
    private static $controllers = null;

    private $app;

    public function __construct()
    {
        $this->app = App::cms();
        if (null === self::$controllers) {
            self::$controllers = $this->app->getControllers();
        }
    }

    public function route(RouteEvent $event)
    {
        $routePieces = explode('/', $event->getRoute());

        // Проверяем путь в списке контроллеров
        if (isset(self::$controllers[$routePieces[0]])) {
            if (!isset($routePieces[1])) {
                return false;
            }

            $resolver = $this->app->getResolver();
            $event->getRequest()->setController($routePieces[0]);
            $event->getRequest()->setAction($routePieces[1]);
            $command = $resolver->resolveController($event->getRequest());

//            $relectionClass = new ReflectionClass($command['controller']);

//            if ( $relectionClass->hasMethod( $command['action'] ) ) {
            $controller = $routePieces[0];
            $params = $this->extractAsParams(array_slice($routePieces, 2));
            $event->setRouted(
                array(
                    'controller' => $controller,
                    'action' => $routePieces[1],
                    'params' => $params,
                )
            );
            $event->stopPropagation();
//            }
        }
    }

}

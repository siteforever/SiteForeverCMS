<?php
/**
 * Direct Route
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
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
        $this->app = App::getInstance();
        if ( null === self::$controllers ) {
            self::$controllers = $this->app->getControllers();
        }
    }

    /**
     * @param Request $request
     * @param         $route
     *
     * @return array|bool|mixed
     */
    public function route(Request $request, $route)
    {
        $routePieces = explode( '/', $route );

        // Проверяем путь в списке контроллеров
        if (isset(self::$controllers[$routePieces[0]])) {
            if (!isset($routePieces[1])) {
                return false;
            }

            $resolver = $this->app->getResolver();
            $request->setController($routePieces[0]);
            $request->setAction($routePieces[1]);
            $command = $resolver->resolveController($request);

            $relectionClass = new ReflectionClass($command['controller']);

            if ( $relectionClass->hasMethod( $command['action'] ) ) {
                $controller = $routePieces[ 0 ];
                $params = $this->extractAsParams( array_slice( $routePieces, 2 ) );
                return array(
                    'controller' => $controller,
                    'action' => $routePieces[1],
                    'params' => $params,
                );
            }
        }
        return false;
    }

}

<?php
/**
 * Direct Route
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use Sfcms\Route;
use ReflectionClass;
use App;

class Direct extends Route
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
     * @param $route
     * @return mixed
     */
    public function route( $route )
    {
        $routePieces = explode( '/', $route );

        // Проверяем путь в списке контроллеров
        if ( isset( self::$controllers[ $routePieces[0] ] ) ) {
            if ( ! isset( $routePieces[1] ) ) {
                return false;
            }

            $resolver = $this->app->getResolver();
            $command = $resolver->resolveController( $routePieces[0], $routePieces[1] );

            $relectionClass = new ReflectionClass( $command['controller'] );

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

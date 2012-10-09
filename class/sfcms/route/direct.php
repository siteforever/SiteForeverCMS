<?php
/**
 * Direct Route
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;
use Sfcms\Route;
use App;

class Direct extends Route
{
    private $controllers = array();

    public function __construct()
    {
        $this->controllers = App::getInstance()->getControllers();
    }

    /**
     * @param $route
     * @return mixed
     */
    public function route( $route )
    {
        $routePieces = explode( '/', $route );

        // Проверяем путь в списке контроллеров
        if ( isset( $this->controllers[ $routePieces[0] ] ) ) {
            if ( ! isset( $routePieces[1] ) ) {
                return false;
            }

            $relectionClass = new \ReflectionClass('Controller_'.ucfirst( strtolower( $routePieces[0] ) ) );

            if ( $relectionClass->hasMethod( strtolower( $routePieces[1] ) . 'Action' ) ) {
                $controller = $routePieces[ 0 ];
                $action = $routePieces[ 1 ];
                $params = $this->extractAsParams( array_slice( $routePieces, 2 ) );
                return array(
                    'controller' => $controller,
                    'action' => $action,
                    'params' => $params,
                );
            }
        }
        return false;
    }

}

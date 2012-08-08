<?php
/**
 * Direct Route
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms\Route;

class Direct extends \Sfcms\Route
{
    private $controllers = array();

    public function __construct()
    {
        $this->controllers = require SF_PATH . '/protected/controllers.php';
        if ( ROOT != SF_PATH && file_exists( ROOT . '/protected/controllers.php' ) ) {
            $this->controllers = array_merge( $this->controllers, require ROOT . '/protected/controllers.php' );
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
        if ( array_search( $routePieces[0], $this->controllers ) ) {
            if ( isset( $routePieces[1] ) ) {
                $relectionClass = new \ReflectionClass('Controller_'.ucfirst( strtolower( $routePieces[0] ) ) );
                if ( $relectionClass->hasMethod( strtolower( $routePieces[1] ) . 'Action' ) ) {
                    $controller = $routePieces[ 0 ];
                    $action = $routePieces[1];
                    $params = $this->extractAsParams( array_slice( $routePieces, 2 ) );
                    return array(
                        'controller' => $controller,
                        'action' => $action,
                        'params' => $params,
                    );
                }
            }
        }
        return false;
    }

}

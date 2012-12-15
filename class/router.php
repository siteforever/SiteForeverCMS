<?php
/**
 * Определяет, каким контроллером обрабатывать запрос
 * @author keltanas aka Ermin Nikolay
 * @link http://ermin.ru
 */

use \Sfcms\Route as Route;

class Router
{
    private $route;

    private $route_table = array();

    private $controller = 'page';
    private $action = 'index';
    private $id = null;

    /** @var \Request */
    private $request;

    private $system = 0;

    private $template = 'index';

    private $_isAlias = false;

    private $_params = array();

    private $_routes = array();

    /**
     * Создаем маршрутизатор
     * @param Request $request
     */
    public function __construct( Request $request )
    {
        $this->request = $request;
        $route         = $this->request->get( 'route' );
        $this->setRoute( $route );

        $this->addRouteRegulation( new Route\Direct );
        $this->addRouteRegulation( new Route\XML );
        $this->addRouteRegulation( new Route\Structure );
        $this->addRouteRegulation( new Route\Defaults );
    }

    /**
     * Add route to table
     * @param \Sfcms\Route $route
     */
    protected function addRouteRegulation( Route $route )
    {
        $this->_routes[] = $route;
    }

    /**
     * Произошел поиск по алиасу
     * @return bool
     */
    public function isAlias()
    {
        return $this->_isAlias;
    }

    /**
     * Вернет href для ссылки
     * @param string $result
     * @param array  $params
     * @return string
     */
    public function createLink( $result = '', $params = array() )
    {
        if( ! $result && isset( $params[ 'controller' ] ) ) {
            return $this->createDirectRequest( $params );
        }

        $result = trim( $result, '/' );
        if( '' === $result ) {
            $result = $this->request->get( 'route' );
        }

        $par = array();

        if ( preg_match( '@/[\w\d]+=[\d]+@i', $result, $matches ) ) {
            foreach ( $matches as $match ) {
                $result = str_replace( $match, '', $result );
                $match  = trim( $match, '/' );
                list( $key ) = explode( '=', $match );
                $par[ $key ] = $match;
            }
        }

        if( count( $params ) ) {
            foreach( $params as $key => $val ) {
                $par[ $key ] = $key . '=' . $val;
            }
        }

        if( 'index' == $result && count( $par ) == 0 ) {
            $result = '';
        }

        $prefix = '/';
        if ( preg_match('@^(http:\/\/|#)@i', $result) ) {
            $prefix = '';
            $par = array();
        }

        if( App::getInstance()->getConfig()->get( 'url.rewrite' ) ) {
            $result = $prefix . $result . ( count( $par ) ? '/' . join( '/', $par ) : '' );
        } else {
            $result = $prefix . '?route=' . $result . ( count( $par ) ? '&' . join( '&', $par ) : '' );
        }

        $result = preg_match('/\.[a-z0-9]{2,4}$/i', $result) ? $result : strtolower( $result );

        return $result;
    }

    /**
     * @param $controller
     * @param string $action
     * @param array $params
     * @return string
     */
    public function createServiceLink( $controller, $action = 'index', $params = array() )
    {
        $result    = '';
        $parstring = '';
        foreach( $params as $key => $param ) {
            $parstring .= '/' . $key . '/' . $param;
        }

        $result .= '/' . $controller;
        if( 'index' != $action || '' != $parstring ) {
            $result .= '/' . $action . $parstring;
        }

        if( ! App::getInstance()->getConfig()->get( 'url.rewrite' ) ) {
            $result = '/?route=' . trim( $result, '/' );
        }

        if( 'index' == $action && 'index' == $controller && '' == $parstring ) {
            $result = '/';
        }

        return strtolower( $result );
    }


    /**
     * @param array $params
     * @return string
     */
    private function createDirectRequest( $params )
    {
        $controller = $params[ 'controller' ];
        unset( $params[ 'controller' ] );
        if( isset( $params[ 'action' ] ) ) {
            $action = $params[ 'action' ];
            unset( $params[ 'action' ] );
        } else {
            $action = 'index';
        }

        return $this->createServiceLink( $controller, $action, $params );
    }

    /**
     * Фильтрует параметры, указанные через key=val
     * @param $route
     * @return string
     */
    public function filterEqParams( $route )
    {
        $result = $route;

        if( preg_match_all( '@(\/\w+=[\w-]+)@xui', $route, $m ) ) {
            foreach( $m[ 0 ] as $par ) {
                $result = str_replace( $par, '', $result );
                $par    = trim( $par, '/' );
                list( $key, $val ) = explode( '=', $par );
                $this->_params[ $key ] = $val;
            }
        }

        return $result;
    }

    /**
     * Маршрутизация
     * @param bool $greedy Показывает, проводитьли "жадную" маршрутизацию?
     * @return bool
     */
    public function routing( $greedy = false )
    {
        $start = microtime(1);
        $this->_params = array();
        // Если контроллер указан явно, то не производить маршрутизацию
        if( ! $greedy && $this->request->getController() ) {
            if( ! $this->request->getAction() ) {
                $this->request->set( 'action', 'index' );
            }
            return true;
        }
        $this->route = trim( $this->route, '/ ' );
        if( $this->route ) {
            $this->route = $this->filterEqParams( $this->route );
        } else {
            $this->route = 'index';
        }
        $routed = false;
        /** @var \Sfcms\Route $route */
        foreach ( $this->_routes as $route ) {
            if ( $routed = $route->route( $this->route ) ) {
                $this->request->setController( $routed['controller'] );
                $this->request->setAction( $routed['action'] );
                if ( isset( $routed['params'] ) && is_array( $routed['params'] ) ) {
                    $this->_params = array_merge( $routed['params'], $this->_params );
                }
                foreach ( $this->_params as $key => $val ) {
                    $this->request->set( $key, $val );
                }
                break;
            }
        }
        App::getInstance()->getLogger()->log( round( microtime(1) - $start, 3 ).' sec', 'Routing' );
        if ( ! $routed ) {
            $this->activateError();
        }
        return true;
    }

    /**
     * Создать ошибку 404
     * @param string $error
     */
    public function activateError( $error = '404' )
    {
        $this->controller = 'page';
        $this->action     = 'error404';
        $this->system     = 0;
    }

    /**
     * Если найдет алиас, то пререопределит маршрут
     * @return bool
     */
    private function findAlias()
    {
        $model = Sfcms_Model::getModel( 'Alias' );
        $alias = $model->find(
            array(
                'cond'  => 'alias = ?',
                'params'=> array( $this->route ),
            )
        );
        if( $alias ) {
            $this->setRoute( $alias->url );
            $this->_isAlias = true;
            return true;
        }
        return false;
    }

    /**
     * Поиск маршрута в таблице БД
     * @return bool
     */
    private function findTableRoute()
    {
        $routes = Sfcms_Model::getModel( 'Routes' );

        $this->route_table = $routes->findAll( 'active = 1' );

        // индексируем маршруты
        foreach( $this->route_table as $route )
        {
            // если маршрут совпадает с алиасом, то сохраняем
            if( preg_match( '@^' . $route[ 'alias' ] . '$@ui', $this->route ) ) {
                $this->controller = $route[ 'controller' ];
                $this->action     = $route[ 'action' ];
                if( isset( $route[ 'id' ] ) ) $this->id = $route[ 'id' ];
                if( isset( $route[ 'system' ] ) ) $this->system = $route[ 'system' ];

                return true;
            }
        }
        return false;
    }

    /**
     * @return int
     */
    public function isSystem()
    {
        return $this->system;
    }

    /**
     * @param $route
     * @param array $params
     * @return Router
     */
    public function setRoute( $route, array $params = array() )
    {
        $route = is_array( $route ) ? reset( $route ) : $route;
        $this->route = trim( $route, '/' );
        $this->controller = 'index';
        $this->action = 'index';
        $this->id = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }
}

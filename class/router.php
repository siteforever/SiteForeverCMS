<?php
/**
 * Определяет, каким контроллером обрабатывать запрос
 * @author keltanas aka Ermin Nikolay
 * @link http://ermin.ru
 */

class Router
{
    private $route;

    private $route_table = array();

    private $controller = 'page';
    private $action     = 'index';
    private $id;

    private $request;

    private $system = 0;

    private $template   = 'index';

    private $_isAlias   = false;

    /**
     * Создаем маршрутизатор
     * @param Request $request
     */
    public function __construct( Request $request )
    {
        $this->request = $request;
        $route = $this->request->get('route');

        $this->setRoute( $route );

//        $this->request->set('route', $this->route);
    }

    /**
     * Произошел поиск по алиасу
     * @return bool
     */
    public function isAlias()
    {
        return  $this->_isAlias;
    }

    /**
     * Вернет href для ссылки
     * @param string $result
     * @param array  $params
     * @return string
     */
    public function createLink( $result = '', $params = array() )
    {
        if ( ! $result && isset( $params['controller'] ) ) {
            return $this->createDirectRequest( $params );
        }

        $result = trim($result, '/');
        if ( '' === $result ) {
            $result = $this->request->get('route');
        }

        $par = array();

        $result = preg_replace('@/[\w\d]+=[\d]+@i', '', $result);

        if ( count($params) ) {
            foreach( $params as $k => $v ) {
                $par[] = $k.'='.$v;
//                $result = preg_replace("@/{$k}=\d+@", '', $result);
            }
        }

        if ( 'index' == $result && count($par) == 0 ) {
            $result = '';
        }

        if ( App::getInstance()->getConfig()->get('url.rewrite') ) {
            $result = '/'.$result.( count($par) ? '/'.join('/', $par) : '' );
        }
        else {
            $result = '/?route='.$result.( count($par) ? '&'.join('&', $par) : '' );
        }

        return strtolower($result);
    }

    /**
     * @param $controller
     * @param string $action
     * @param array $params
     * @return string
     */
    public function createServiceLink( $controller, $action = 'index', $params = array() )
    {
        $result     = '';
        $parstring  = '';
        foreach ( $params as $key => $param ) {
            $parstring .= '/'.$key.'/'.$param;
        }

        $result .= '/'.$controller;
        if ('index' != $action || '' != $parstring ) {
            $result .= '/'.$action . $parstring;
        }

        if ( ! App::getInstance()->getConfig()->get('url.rewrite') ) {
            $result = '/?route='.trim( $result, '/' );
        }

        if ('index' == $action && 'index' == $controller && '' == $parstring ) {
            $result = '/';
        }

        return strtolower($result);
    }

    /**
     * @param array $params
     * @return string
     */
    private function createDirectRequest( $params )
    {
        $controller = $params['controller'];
        unset( $params['controller'] );
        if ( isset( $params['action'] ) ) {
            $action = $params['action'];
            unset( $params['action'] );
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

        if ( preg_match_all( '@(\/\w+=\w+)@xui', $route, $m ) ) {
            foreach ( $m[0] as $par ) {
                $result = str_replace( $par, '', $result );
                $par    = trim($par,'/');
                list( $key, $val ) = explode( '=', $par );
                $this->request->set($key, $val);
            }
//            var_export( $m );
        }

        return $result;
    }

    /**
     * Маршрутизация
     * @return bool
     */
    public function routing()
    {
        // Если контроллер и действие указаны явно, то не производить маршрутизацию
        if ( $this->request->get('controller') ) {
            if ( ! $this->request->get('action') ) {
                $this->request->set('action', 'index');
            }
            return true;
        }


        $this->route = trim( $this->route, '/' );

        if ( ! $this->route ) {
            $this->route = 'index';
        }

        if ( preg_match( '/[\w\d\/_-]+/i', $this->route ) ) {
            $this->route = trim( $this->route, ' /' );
        }

        $this->route    = $this->filterEqParams( $this->route );

//        // выделяем указатель на страницы
//        if ( preg_match( '/\/page(\d+)/i', $this->route, $match_page ) ) {
//            $this->request->set('page', $match_page[1]);
//            $this->route = trim( str_replace( $match_page[0], '', $this->route ), '/' );
//        }

        // ----------------

        $this->findAlias();

        if ( ! $this->findRoute() )
        {
            if ( ! $this->findStructure() ) {
                $route_pieces   = explode( '/', $this->route );

//                foreach( $route_pieces as $key => $param ) {
//                    if ( preg_match( '/(\w+)=(.+)/xui', $param, $matches ) ) {
//                        $this->request->set( $matches[1], $matches[2] );
//                        unset( $route_pieces[$key] );
//                        continue;
//                    }
//                }

        //        $this->request->set('params', $route_pieces);
        //        $this->route = join('/', $route_pieces);
        //
        //        var_dump( $this->route );

                if ( count( $route_pieces ) == 1 ) {
                    $this->controller   = $route_pieces[0];
                    $this->action   = 'index';
                }
                elseif ( count( $route_pieces ) > 1 ) {
                    $this->controller   = $route_pieces[0];
                    $this->action       = $route_pieces[1];

                    $route_pieces       = array_slice( $route_pieces, 2 );

                   if ( 0 == count( $route_pieces ) % 2 ) {
                        $key    = '';
                        foreach ( $route_pieces as $i => $r ) {
                            if ( $i % 2 ) {
//                                if ( null === $this->request->get($key) ) {
                                $this->request->set( $key, $r );
//                                print "key=$key,val=$r\n";
//                                }
                            } else {
                                $key    = $r;
                            }
                        }
                    }
                }
                else {
                    $this->activateError();
                }
            }
        }

        $this->request->set('controller', $this->controller);
        $this->request->set('action',     $this->action);
        if ( $this->id && null === $this->request->get('id') ) {
            $this->request->set('id', $this->id);
        }
        if ( $this->template ) {
            $this->request->set('template',   $this->template);
        }
        return true;
    }

    /**
     * Создать ошибку 404
     * @param string $error
     */
    public function activateError( $error = '404' )
    {
        $this->controller   = 'page';
        $this->action       = 'error';
        $this->id           = $error;
        $this->template     = App::getInstance()->getConfig()->get('template.404');
        $this->system       = 0;
    }

    /**
     * Если найдет алиас, то пререопределит маршрут
     * @return bool
     */
    private function findAlias()
    {
        $model  = Model::getModel('Alias');
        $alias  = $model->find(
            array(
                'cond'  => 'alias = ?',
                'params'=> array($this->route),
            )
        );

        if ( $alias ) {
            $this->setRoute( $alias->url );
            $this->_isAlias = true;
            return true;
        }
        return false;
    }

    /**
     * Поиск по маршрутам
     * @return bool
     */
    private function findRoute()
    {
        if ( $this->findXMLRoute() ) {
            return true;
        } elseif ( $this->findTableRoute() ) {
            return true;
        }
        return false;
    }

    /**
     * Ищем маршрут в XML конфиге
     * @return bool
     */
    private function findXMLRoute()
    {
        $xml_routes_file    = SF_PATH.'/protected/routes.xml';
        if ( file_exists( $xml_routes_file ) ) {
            $xml_routes = new SimpleXMLIterator( file_get_contents( $xml_routes_file ) );
            if ( $xml_routes ) {
                foreach ( $xml_routes as $route ) {
                    if ( $route['active'] !== "0" && preg_match( '@^'.$route['alias'].'$@ui', $this->route ) ) {
                        $this->controller   = (string) $route->controller;
                        $this->action       = isset($route->action) ? (string) $route->action : 'index';
                        $this->id           = $route['id'];
                        $this->protected    = $route['protected'];
                        $this->system       = $route['system'];
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Поиск маршрута в таблице БД
     * @return bool
     */
    private function findTableRoute()
    {
        $routes = Model::getModel('Routes');

        $this->route_table = $routes->findAll( array(
            'cond' => 'active = 1',
        ));

        // индексируем маршруты
        foreach( $this->route_table as $route )
        {
            // если маршрут совпадает с алиасом, то сохраняем
            if ( preg_match( '@^'.$route['alias'].'$@ui', $this->route ) )
            {
                $this->controller   = $route['controller'];
                $this->action       = $route['action'];
                if ( isset($route['id']) )      $this->id       = $route['id'];
                if ( isset($route['system']) )  $this->system   = $route['system'];

                return true;
            }
        }
        return false;
    }

    /**
     * Поиск по структуре
     * @return bool
     */
    private function findStructure()
    {
        $model  = Model::getModel('Page');

        $data   = $model->find(array(
            'cond'  => 'alias = ? AND deleted = 0',
            'params'=> array($this->route),
        ));

        if ( $data )
        {
            $this->controller   = $data['controller'];
            $this->action       = $data['action'];
            $this->id           = $data['id'];
            $this->template     = $data['template'];
            $this->system       = $data['system'];
            return true;
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
     * @return Router
     */
    public function setRoute($route)
    {
        $this->route = trim($route,'/');
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

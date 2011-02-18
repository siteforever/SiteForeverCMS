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
    private $action = 'index';
    private $id;

    private $request;

    private $system = 0;

    private $template = 'index';

    /**
     * Создаем маршрутизатор
     * @return void
     */
    function __construct( Request $request )
    {
        $this->request = $request;
        $route = $this->request->get('route');

        if ( is_array( $route ) ) {
            $route = 'index';
        }

        $this->route = trim( $route, '/' );

        /*if ( $this->route == 'index' ) {
            header("location: /");
            exit();
        }*/

        if ( ! $this->route ) {
            $this->route = 'index';
        }

        if ( preg_match( '/[\w\d\/_-]+/i', $this->route ) ) {
            $this->route = trim( $this->route, ' /' );
        }

        // выделяем указатель на страницы
        if ( preg_match( '/\/page(\d+)/i', $this->route, $match_page ) ) {
            $this->request->set('page', $match_page[1]);
            $this->route = trim( str_replace( $match_page[0], '', $this->route ), '/' );
        }

        $params = explode('/', $this->route);


        foreach( $params as $key => $param ) {
            if ( preg_match( '/(\w+)=(.+)/xui', $param, $matches ) ) {
                $this->request->set( $matches[1], $matches[2] );
                unset( $params[$key] );
            }
        }


        $this->request->set('params', $params);

        $this->route = join('/', $params);

        //print $this->route;
        //printVar( App::$request->get('email') );
        //printVar( App::$request->get('code') );

        $this->request->set('route', $this->route);
    }

    /**
     * Вернет href для ссылки
     * @param string $url
     * @param array  $params
     * @return string
     */
    function createLink( $url = '', $params = array() )
    {
        $url = trim($url, '/');

        if ( $url == '' ) {
            $url = $this->request->get('route');
        }

        $par = array();

        if ( count($params) ) {
            foreach( $params as $k => $v ) {
                $par[] = $k.'='.$v;
            }
        }

        if ( App::getInstance()->getConfig()->get('url.rewrite') ) {
            $url = '/'.$url.( count($par) ? '/'.join('/', $par) : '' );
        }
        else {
            $url = '/index.php?route='.$url.( count($par) ? '&'.join('&', $par) : '' );
        }

        return $url;
    }

    /**
     * Маршрутизация
     * @return void
     */
    function routing()
    {
        // Если контроллер и действие указаны явно, то не производить маршрутизацию
        if ( $this->request->get('controller') ) {
            if ( ! $this->request->get('action') ) {
                $this->request->set('action', 'index');
            }
            return;
        }

        if ( ! $this->findRoute() ) {
            $this->findStructure();
        }

        $this->request->set('controller', $this->controller);
        $this->request->set('action',     $this->action);
        $this->request->set('id',         $this->id);
        $this->request->set('template',   $this->template);
    }


    /**
     * Поиск по маршрутам
     * @return bool
     */
    function findRoute()
    {
        // сначала ищем маршрут в XML
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


        // Далее ищем маршрут в таблице
        $routes = Model::getModel('Routes');

        $this->route_table = $routes->findAll( array(
            'cond' => 'active = 1',
        ));


        // индексируем маршруты
        foreach( $this->route_table as $route )
        {
            // var_dump('@^'.$route['alias'].'$@ui', $this->route);
            // print '<br />';
            // если маршрут совпадает с алиасом, то сохраняем
            if ( preg_match( '@^'.$route['alias'].'$@ui', $this->route ) )
            {
                $this->controller   = $route['controller'];
                $this->action       = $route['action'];
                $this->id           = $route['id'];
                $this->system       = $route['system'];

                return true;
            }
        }
        return false;
    }

    /**
     * Поиск по структуре
     * @return void
     */
    function findStructure()
    {
        $model  = Model::getModel('Structure');

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
        }
        else {
            $this->controller   = 'page';
            $this->action       = 'error';
            $this->id           = '404';
            $this->template     = App::getInstance()->getConfig()->get('template.404');
            $this->system       = 0;
        }
    }

    function isSystem()
    {
        return $this->system;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }
}

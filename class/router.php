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

        if ( $this->route == 'index' ) {
            header("location: /");
            exit();
        }


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
     */
    function createLink( $url = '', $params = array() )
    {
        $url = trim($url, '/');

        if ( $url == '' ) {
            $url = $this->request->get('route');
        }

        $url = '/'.$url.'/';

        $par = array();

        if ( count($params) ) {
            foreach( $params as $k => $v ) {
                $par[] = $k.'='.$v;
            }
        }
        if ( count($par) ) {
            $par = join('/', $par).'/';
            $url .= $par;
        }

        if ( ! REWRITEURL ) {
            $url = "/index.php?route=".$url;
        }
        return $url;
    }

    /**
     * Маршрутизация
     * @return void
     */
    function routing()
    {
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


        $route = Model::getModel('model_Routes');

        $this->route_table = $route->findAll( array(
            'cond' => "active = 1",
        ));

        //Error::dump($this->route_table);

        // индексируем маршруты
        foreach( $this->route_table as $route )
        {
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
        if ( $data = App::$structure->findByRoute( $this->route ) )
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
            $this->template     = App::$config->get('template.404');
            $this->system       = 0;
        }
    }

    function isSystem()
    {
        return $this->system;
    }
}

<?php
/**
 * Определяет, каким контроллером обрабатывать запрос
 * @author keltanas aka Ermin Nikolay
 * @link   http://ermin.ru
 */
namespace Sfcms;

use Sfcms\Route;
use Sfcms\Request;
use App;

class Router
{
    private $route;

    private $id = null;

    /** @var Request */
    private $request;

    /** @var int Was system request */
    private $system = 0;

    private $_isAlias = false;

    private $_params = array();

    /** @var array Routes handlers */
    private $_routes = array();

    /**
     * Создаем маршрутизатор
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $route         = $this->request->get('route');
        $this->setRoute($route);

        $this->addRouteHandler(new Route\DirectRoute($request));
        $this->addRouteHandler(new Route\XmlRoute($request));
        $this->addRouteHandler(new Route\StructureRoute($request));
        $this->addRouteHandler(new Route\DefaultRoute($request));
    }

    /**
     * Add route to table
     *
     * @param \Sfcms\Route $route
     */
    protected function addRouteHandler(Route $route)
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
     *
     * @param string $result
     * @param array  $params
     *
     * @return string
     */
    public function createLink($result = '', $params = array())
    {
        if (!$result && isset($params['controller'])) {
            return static::createDirectRequest($params);
        }

        $result = trim($result, '/');
        if ('' === $result) {
            $result = $this->request->get('route');
        }

        $par = array();

        if (preg_match('@/[\w\d]+=[\d]+@i', $result, $matches)) {
            foreach ($matches as $match) {
                $result = str_replace($match, '', $result);
                $match  = trim($match, '/');
                list($key) = explode('=', $match);
                $par[$key] = $match;
            }
        }

        if (count($params)) {
            foreach ($params as $key => $val) {
                $par[$key] = $key . '=' . $val;
            }
        }

        if ('index' == $result && count($par) == 0) {
            $result = '';
        }

        $prefix = '/';
        if (preg_match('@^(http:\/\/|#)@i', $result)) {
            $prefix = '';
            $par    = array();
        }

        if (App::getInstance()->getConfig()->get('url.rewrite')) {
            $result = $prefix . $result . (count($par) ? '/' . join('/', $par) : '');
        } else {
            $result = $prefix . '?route=' . $result . (count($par) ? '&' . join('&', $par) : '');
        }

        $result = preg_match('/\.[a-z0-9]{2,4}$/i', $result) ? $result : strtolower($result);

        return $result;
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array  $params
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function createServiceLink($controller, $action = 'index', $params = array())
    {
        $result    = '';
        $parstring = '';
        if (null === $controller) {
            throw new \InvalidArgumentException('$controller must not be null');
        }
        foreach ($params as $key => $param) {
            $parstring .= '/' . $key . '/' . $param;
        }

        $result .= '/' . $controller;
        if ('index' != $action || '' != $parstring) {
            $result .= '/' . $action . $parstring;
        }

        if (!App::getInstance()->getConfig()->get('url.rewrite')) {
            $result = '/?route=' . trim($result, '/');
        }

        if ('index' == $action && 'index' == $controller && '' == $parstring) {
            $result = '/';
        }

        return strtolower($result);
    }


    /**
     * @param array $params
     *
     * @return string
     */
    private static function createDirectRequest($params)
    {
        $controller = $params['controller'];
        unset($params['controller']);
        if (isset($params['action'])) {
            $action = $params['action'];
            unset($params['action']);
        } else {
            $action = 'index';
        }

        return static::createServiceLink($controller, $action, $params);
    }

    /**
     * Фильтрует параметры, указанные через key=val
     *
     * @param $route
     *
     * @return string
     */
    public function filterEqParams($route)
    {
        $result = $route;

        if (preg_match_all('@(\/\w+=[\w-]+)@xui', $route, $m)) {
            foreach ($m[0] as $par) {
                $result = str_replace($par, '', $result);
                $par    = trim($par, '/');
                list($key, $val) = explode('=', $par);
                $this->_params[$key] = $val;
            }
        }

        return $result;
    }

    /**
     * Маршрутизация
     * @param bool $greedy Показывает, проводить ли "жадную" маршрутизацию?
     * Жадная маршрутизация выполняется в любом случае. Не жадная, только есть не выбран контроллер.
     * Например, контроллер можно указать явно во время запроса.
     *
     * @return bool
     */
    public function routing($greedy = false)
    {
        $start = microtime(1);
        // Если контроллер указан явно, то не производить маршрутизацию
        if (!$greedy && $this->request->getController()) {
            if (!$this->request->getAction()) {
                $this->request->set('_action', 'index');
            }

            return true;
        }
        if ($this->route) {
            $this->route = $this->filterEqParams($this->route);
        } else {
            $this->route = 'index';
        }
        $routed = false;
        /** @var \Sfcms\Route $route */
        foreach ($this->_routes as $route) {
            if ($routed = $route->route($this->route)) {
                $this->request->setController($routed['controller']);
                $this->request->setAction($routed['action']);
                if (isset($routed['params']) && is_array($routed['params'])) {
                    $this->_params = array_merge($routed['params'], $this->_params);
                }
                foreach ($this->_params as $key => $val) {
                    $this->request->query->set($key, $val);
                }
                break;
            }
        }
        App::getInstance()->getLogger()->log(round(microtime(1) - $start, 3) . ' sec', 'Routing');
        if (!$routed) {
            $this->activateError();
        }

        return true;
    }

    /**
     * Создать ошибку 404
     * @param string $error
     */
    public function activateError($error = '404')
    {
        $this->request->setController('page');
        $this->request->setAction('error404');
        $this->system = 0;
    }

    /**
     * @return int
     */
    public function isSystem()
    {
        return $this->system;
    }

    /**
     * @param       $route
     * @param array $params
     *
     * @return Router
     */
    public function setRoute($route, array $params = array())
    {
        $route = is_array($route) ? reset($route) : $route;
        $this->route = trim($route, '/');
        $this->request->query->set('route', $this->route);
//        $this->request->setController('page');
//        $this->request->setAction('index');
//        $this->request->setModule('page');
        $this->_params = $params;
        $this->id      = null;

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

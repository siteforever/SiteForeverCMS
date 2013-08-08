<?php
/**
 * Определяет, каким контроллером обрабатывать запрос
 * @author keltanas aka Ermin Nikolay
 * @link   http://ermin.ru
 */
namespace Sfcms;

use Sfcms\Route;
use Sfcms\Request;
use Sfcms\LoggerInterface;

class Router
{
    private $route;

    private $id = null;

    /** @var Request */
    private $request;

    private static $rewrite = false;

    /** @var int Was system request */
    private $system = 0;

    private $_isAlias = false;

    private $_params = array();

    /** @var array Routes handlers */
    private $_routes = array();

    /** @var LoggerInterface */
    private $logger = null;

    /** @var  bool */
    protected static $debug;

    /**
     * Создаем маршрутизатор
     */
    public function __construct($debug = true)
    {
        static::$debug = $debug;
//        $this->addRouteHandler(new Route\DirectRoute());
//        $this->addRouteHandler(new Route\XmlRoute());
        $this->addRouteHandler(new Route\YmlRoute());
        $this->addRouteHandler(new Route\StructureRoute());
//        $this->addRouteHandler(new Route\DefaultRoute());
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        if ($this->request->get('route')) {
            $this->setRoute('/' . trim($this->request->get('route'), '/'));
        } else {
            $this->setRoute(str_replace($this->request->getScriptName(), '',  $this->request->getRequestUri()));
        }
        return $this;
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
     * @param boolean|array $rewrite
     */
    public function setRewrite($rewrite)
    {
        if (is_array($rewrite)) {
            if (isset($rewrite['rewrite'])) {
                $rewrite = $rewrite['rewrite'];
            } else {
                $rewrite = false;
            }
        }
        self::$rewrite = $rewrite;
    }

    /**
     * @return boolean
     */
    public function getRewrite()
    {
        return self::$rewrite;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
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
        if ('' === $result && $this->request) {
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
        if (preg_match('@^(https?:\/\/|#)@i', $result)) {
            $prefix = '';
            $par    = array();
        }

        if ($this->getRewrite()) {
            $result = $prefix . $result . (count($par) ? '?' . join('&', $par) : '');
        } else {
//            $result = $prefix . '?route=' . $result . (count($par) ? '&' . join('&', $par) : '');
            $result = $prefix . 'index.php/' . $result . (count($par) ? '?' . join('&', $par) : '');
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
        if (null === $controller) {
            throw new \InvalidArgumentException('$controller must not be null');
        }
        $vars = array();
        foreach ($params as $key => $param) {
            $vars[] = $key . '=' . $param;
        }

        $result .= '/' . $controller;
        if ('index' != $action || $vars) {
            $result .= '/' . $action . ($vars ? '?' . join('&', $vars) : '');
        }

        if (!self::$rewrite) {
            $result = '/index.php/' . trim($result, '/?');
        }

        if ('index' == $action && 'index' == $controller && !$vars) {
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
                $this->request->setAction('index');
            }

            return true;
        }
        if (!trim($this->route, '?&/ ')) {
            $this->route = '/';
        }
        $routed = false;
        /** @var \Sfcms\Route $route */
        foreach ($this->_routes as $route) {
            if ($routed = $route->route($this->request, $this->route)) {
                if (is_array($routed)) {
                    $this->request->setController($routed['controller']);
                    $this->request->setAction($routed['action']);
                    if (isset($routed['params']) && is_array($routed['params'])) {
                        $this->_params = array_merge($routed['params'], $this->_params);
                    }
                    foreach ($this->_params as $key => $val) {
                        $this->request->query->set($key, $val);
                    }
                }
                break;
            }
        }
        if (null !== $this->getLogger()) {
            $this->getLogger()->info('Routing (' . round(microtime(1) - $start, 3) . ' sec)');
        }
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
        $this->request->setController('error');
        $this->request->setAction('error' . $error);
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
        $this->route = rtrim(parse_url($route, PHP_URL_PATH), '/');
        $query = parse_url($route, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $params);
            foreach ($params as $key => $val) {
                $this->request->query->set($key, $val);
            }
        }
        $this->request->query->set('route', $this->route);
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

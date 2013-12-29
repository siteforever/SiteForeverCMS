<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Controller;

use Sfcms\Controller;
use ReflectionClass;
use RuntimeException;
use Sfcms\Kernel\AbstractKernel;
use Sfcms\Request;
use Sfcms_Http_Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Resolver
{
    /** @var array */
    protected $_controllers;

    /** @var \App */
    protected $app;

    public function __construct(AbstractKernel $app)
    {
        $this->app = $app;
        $this->_controllers = $app->getControllers();
    }

    /**
     * Берет данные через метод $this->app()->getControllers() и на основе этого
     * конфига принимает решение о том, какой класс должен выполнять функции контроллера,
     * в каком файле и пространстве имен он находится.
     *
     * @param Request $request
     * @param $controller
     * @param $action
     * @param $moduleName
     *
     * @return array
     * @throws HttpException
     * @throws RuntimeException
     */
    public function resolveController(Request $request, $controller = null, $action = null, $moduleName = null)
    {
        if (null === $controller) {
            $controller = strtolower($request->getController());
        }
        if (null === $action) {
            $action = $request->getAction();
        }
        $actionMethod = strtolower($action) . 'Action';

        // Если не удалось определить контроллер, как известный, то инициировать ош. 404
        if (!isset($this->_controllers[$controller])) {
            throw new HttpException(404, sprintf('Controller "%s" not found', $controller));
        }

        if (!is_array($this->_controllers[$controller])) {
            throw new RuntimeException(sprintf('Configuration of the controller "%s" should be an array', $controller));
        }

        $config = $this->_controllers[$controller];
        $moduleName = isset($config['module']) ? $config['module'] : $moduleName;

        if (isset($config['class'])) {
            $controllerClass = $config['class'];
        } else {
            // compatibility with old style
            $controllerClass = 'Controller_' . ucfirst($controller);
        }

        if ($moduleName) {
            $module = $this->app->getModule($moduleName);
            $controllerClass = sprintf(
                '%s\\%sController',
                $module->getNs(),
                str_replace('_', '\\', $controllerClass)
            );
        }

        return array('controller' => $controllerClass, 'action' => $actionMethod, 'module' => $moduleName);
    }

    /**
     * Запуск контроллера
     * @param Request $request
     * @param array $command
     * @return null|string
     * @throws HttpException
     */
    public function dispatch(Request $request, array $command = array())
    {
        $result = null;

        if (!$command) {
            $command = $this->resolveController($request);
            if (!$command) {
                throw new HttpException(404, 'Controller not resolved');
            }
        }

        $this->app->getLogger()->info('Command', $command);

        if (!class_exists($command['controller'])) {
            throw new HttpException(404, sprintf('Controller class "%s" not exists', $command['controller']));
        }

        $ref = new ReflectionClass($command['controller']);

        /** @var Controller $controller */
        $controller = $ref->newInstance($request);
        $controller->setContainer($this->app->getContainer());
        $controller->init();

        // Защита системных действий
        $access = $controller->access();

        $this->acl($request, $access, $command);

        try {
            $method = $ref->getMethod($command['action']);
        } catch(\ReflectionException $e) {
            throw new HttpException(404, $e->getMessage());
        }
        $this->app->getTpl()->assign('this', $controller);
        $this->app->getTpl()->assign('request', $request);
        foreach ($this->app->getContainer()->getParameterBag()->all() as $key => $parameters ) {
            $this->app->getTpl()->assign($key, $parameters);
        }
        if ($controller instanceof EventSubscriberInterface) {
            $this->app->getEventDispatcher()->addSubscriber($controller);
        }

        $arguments = $this->prepareArguments($method, $request);
        $this->app->getLogger()->info('Invoke controller', $arguments);
        $result = $method->invokeArgs($controller, $arguments);

        return $result;
    }


    /**
     * Подотавливает список аргументов для передачи в Action, на основе указанных параметров и проверяет типы
     * на основе правил, указанных в DocBlock
     * @param \ReflectionMethod $method
     * @param Request $request
     * @return array
     */
    protected function prepareArguments(\ReflectionMethod $method, Request $request)
    {
        $arguments    = array();
        $methodParams = $method->getParameters();
        $docComment   = $method->getDocComment();
        preg_match_all('/@param (int|float|string|array) \$([\w_-]+)/', $docComment, $match);
        foreach ($methodParams as $param) {
            $default = $param->isOptional() ? $param->getDefaultValue() : null;
            if ($request->query->has($param->name) || $request->attributes->has($param->name)) {
                // Фильтруем входные параметры
//                $arguments[$param->name] = $val;
                if (false !== ($key = array_search($param->name, $match[2]))) {
                    switch ($match[1][$key]) {
                        case 'int':
                            $arguments[$param->name] =
                                $request->attributes->getDigits($param->name,
                                    $request->query->getDigits($param->name, $default)
                                );
                            break;
                        case 'float':
                            $arguments[$param->name] =
                                $request->attributes->filter($param->name,
                                    $request->query->filter($param->name, $default, false, FILTER_VALIDATE_FLOAT),
                                    false, FILTER_VALIDATE_FLOAT);
                            break;
                        case 'string':
                            $arguments[$param->name] =
                                $request->attributes->filter($param->name,
                                $request->query->filter($param->name, $default, false, FILTER_SANITIZE_STRING),
                                false, FILTER_SANITIZE_STRING);
                            break;
                        default:
                            $arguments[$param->name] = $request->get($param->name, $default);
                    }
                }
            } else {
                $arguments[$param->name] = $default;
            }
        }
        return $arguments;
    }

    /**
     * Acl
     *
     * Проходит по массиву, предоставленному методом Access() контроллера
     *
     * Массив содержит в качестве ключей - группы пользователей, а в качестве значений - список методов
     * которые разрешены для этой группы
     */
    protected function acl(Request $request, array $access = null, array $command = array())
    {
        if ( $access && is_array($access) ) {
            foreach( $access as $perm => $ruleMethods ) {
                if ( 'system' == $perm ) {
                    $perm = USER_ADMIN;
                }
                $ruleMethods = is_string($ruleMethods) ? array_map( 'trim', explode(',',$ruleMethods) ) : $ruleMethods;
                if( ! is_array($ruleMethods) ) {
                    throw new RuntimeException('Expected string or array');
                }
                $ruleMethods = array_map(function($method){
                    if (false === stripos($method, 'action')) {
                        $method = strtolower($method) . 'Action';
                    }
                    return $method;
                }, $ruleMethods );
                if (in_array($command['action'], $ruleMethods)) {
                    if ($this->app->getAuth()->hasPermission($perm)) {
                        if ($perm == USER_ADMIN) {
                            $this->setSystemPage($request);
                        }
                    } else {
                        throw new Sfcms_Http_Exception('Access denied', 403);
                    }
                }
            }
        }
    }

    /**
     * Переводит систему на работу с админкой
     */
    private function setSystemPage(Request $request)
    {
        $request->setSystem(true);
        $request->setTemplate('admin');
    }
}

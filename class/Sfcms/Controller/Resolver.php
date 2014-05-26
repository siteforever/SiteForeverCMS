<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Controller;

use Module\System\Event\ControllerEvent;
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
        if (preg_match('/([a-z]+):([a-z]+):?([a-z]*)/i', $controller, $m)) {
            $moduleName = ucfirst($m[1]);
            $controller = $m[2];
            $action =  isset($m[3]) ? $m[3] : 'index';
        }

        if (!$action) {
            $action = $request->getAction();
        }
        $actionMethod = strtolower($action) . 'Action';
        $controllerClass = null;
        if (isset($this->_controllers[$controller])) {
            $config = $this->_controllers[$controller];
            $moduleName = isset($config['module']) ? $config['module'] : $moduleName;
            $controllerClass = isset($config['class']) ? $config['class'] : $controller;
        }

        if (null === $controllerClass) {
            $controllerClass = $controller;
        }

        if (isset($moduleName) && false === strpos($controllerClass, '\\')) {
            $module = $this->app->getModule($moduleName);
            $controllerClass = sprintf(
                '%s\\Controller\\%sController',
                $module->getNs(),
                ucfirst($controllerClass)
            );
        }

        $request->setModule($moduleName);
        $request->setController($controller);
        $request->setAction($action);

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

        $reflectionController = new ReflectionClass($command['controller']);

        /** @var Controller $controller */
        $controller = $reflectionController->newInstance($request);
        $controller->setContainer($this->app->getContainer());

        // Защита системных действий
        $access = $controller->access();

        $this->acl($request, $access, $command);

        try {
            $method = $reflectionController->getMethod($command['action']);
        } catch(\ReflectionException $e) {
            throw new HttpException(404, $e->getMessage());
        }
        $this->app->getTpl()->assign('this', $controller);
        $this->app->getTpl()->assign('request', $request);
        if ($controller instanceof EventSubscriberInterface) {
            $this->app->getEventDispatcher()->addSubscriber($controller);
        }

        $arguments = $this->prepareArguments($method, $request);

        $this->app->getLogger()->info('Invoke controller', $arguments);
        $event = new ControllerEvent($controller, $arguments, $request);
        $this->app->getEventDispatcher()->dispatch(ControllerEvent::RUN_BEFORE, $event);
        $controller->init();
        $result = $method->invokeArgs($controller, $arguments);
        $this->app->getEventDispatcher()->dispatch(ControllerEvent::RUN_AFTER, $event);

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
                        throw new HttpException(403, 'Access denied');
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

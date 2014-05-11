<?php
use Sfcms\Controller;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Команда
 * Вызывает действие указанного контроллера
 *
 * {command name="page"}
 * {command controller="page"}
 * {command name="admin" action="add"}
 * {command controller="admin" action="add"}
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

function smarty_function_command($params)
{
    $app = App::cms();

    if (!isset($params['controller']) && !isset($params['name'])) {
        throw new Exception();
    }

    if (isset($params['name'])) {
        $controller = 'Controller_' . $params['name'];
        unset($params['name']);
    }

    if (isset($params['controller'])) {
        $controller = $params['controller'];
        unset($params['controller']);
    }

    $action = (isset($params['action'])) ? $params['action'] : 'index';

    $action = strtolower($action) . 'Action';

    if (class_exists($controller)) {
        /** @var Controller $command */
        $command = new $controller(Request::createFromGlobals());
        $command->setContainer(App::cms()->getContainer());
        if ($command instanceof \Sfcms\Controller && method_exists($command, $action)) {
            $result = call_user_func([$command, $action]);
        }

        if (is_object($result) && $result instanceof Response) {
            $result = $result->getContent();
        }
        return $result;
    } else {
        return sprintf('command %s not exists', $controller);
    }
}

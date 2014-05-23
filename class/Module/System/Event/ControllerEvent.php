<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Event;

use Sfcms\Controller;
use Sfcms\Request;
use Symfony\Component\EventDispatcher\Event;

class ControllerEvent extends Event
{
    const RUN_BEFORE = 'system.controller.before';
    const RUN_AFTER = 'system.controller.after';

    /** @var Controller  */
    private $controller;

    private $arguments = [];

    /** @var Request */
    private $request;

    function __construct(Controller $controller, $arguments, Request $request)
    {
        $this->controller = $controller;
        $this->arguments = $arguments;
        $this->request = $request;
    }

    /**
     * @return \Sfcms\Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return \Sfcms\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}

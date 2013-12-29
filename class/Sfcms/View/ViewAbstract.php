<?php
/**
 * Интерфейс внешнего представления
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View;

use Sfcms\Kernel\AbstractKernel as Application;
use Sfcms\Request;
use Sfcms\Tpl\Driver;
use Sfcms\Kernel\KernelEvent;

abstract class ViewAbstract
{
    protected $config = array();

    public function __construct(Application $app, array $config)
    {
        $this->_app = $app;
        $this->config = $config;
    }

    /**
     * @return Driver
     */
    protected function getTpl()
    {
        return $this->_app->getTpl();
    }

    /**
     * @abstract
     * @param KernelEvent $event
     * @return KernelEvent
     */
     public abstract function view(KernelEvent $event);
}

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

    public function __construct( Application $app )
    {
        $this->_app = $app;
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
     * @return string
     */
     public abstract function view(KernelEvent $event);
}

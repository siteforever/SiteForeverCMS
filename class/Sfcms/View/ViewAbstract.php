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

    /** @var Application */
    protected $app;

    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
        /** @var $theme string */
        $theme = $this->config['theme'];

        $this->path = array(
            'theme'  => '/themes/' . $theme,
            'css'    => '/themes/' . $theme . '/css',
            'less'    => '/themes/' . $theme . '/less',
            'js'     => '/themes/' . $theme . '/js',
            'images' => '/themes/' . $theme . '/images',
            'img'    => '/themes/' . $theme . '/img',
            'misc'   => '/misc',
        );
    }

    /**
     * @return Driver
     */
    protected function getTpl()
    {
        return $this->app->getTpl();
    }

    /**
     * @abstract
     * @param KernelEvent $event
     * @return KernelEvent
     */
     public abstract function view(KernelEvent $event);
}

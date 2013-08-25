<?php
/**
 * Формируем лэйаут для страниц
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Sfcms\View\Layout;
use Sfcms\Kernel\KernelEvent;

class Page extends Layout
{
    /**
     * @inheritdoc
     */
    public function view(KernelEvent $event)
    {
        $request = $event->getRequest();
        $this->init($request);

        if (!$this->_app->getConfig('misc.noBootstrap')) {
            $this->_app->getAssets()->addStyle('/misc/bootstrap/css/bootstrap.css');
        }

        $this->_app->getAssets()->addStyle($this->getCss() . '/style.css');
        if (file_exists(trim($this->getCss(), '/') . '/print.css')) {
            $this->_app->getAssets()->addStyle($this->getCss() . '/print.css');
        }
        if (file_exists(trim($this->getJs() . '/script.js', '/'))) {
            $this->_app->getAssets()->addScript($this->getJs() . '/script.js');
        }

        $this->getTpl()->assign('response', $event->getResponse());
        $event->getResponse()->setContent($this->getTpl()->fetch(
            $request->get('resource') . $request->getTemplate()
        ));
        return $event;
    }
}

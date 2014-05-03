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

        $this->getTpl()->assign('response', $event->getResponse());
        $event->getResponse()->setContent($this->getTpl()->fetch($request->getTemplate()));
        return $event;
    }
}

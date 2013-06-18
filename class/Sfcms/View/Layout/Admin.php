<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Sfcms\View\Layout;
use Sfcms\Kernel\KernelEvent;

class Admin extends Layout
{
    /**
     * @param KernelEvent $event
     * @return KernelEvent
     */
    public function view(KernelEvent $event)
    {
        $request = $event->getRequest();
        $this->init($request);
        // подключение админских стилей и скриптов
        $this->attachJUI();
        $this->_app->addStyle( $this->getMisc() . '/admin/admin.css' );
        // jQuery

//        $this->attachWysiwyg();

        $this->_app->addStyle( $this->getMisc() . '/elfinder/css/elfinder.css' );

        $this->_app->addScript('/static/admin.js');

        $this->_app->addStyle( $this->getMisc() . '/bootstrap/css/bootstrap.css' );

        $this->getTpl()->assign('response', $event->getResponse());
        $event->getResponse()->setContent($this->getTpl()->fetch(
            $request->get('resource') . $request->getTemplate()
        ));
        return $event;
    }
}

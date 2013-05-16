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
     * @param KernelEvent $event
     * @return KernelEvent
     */
    public function view(KernelEvent $event)
    {
        $request = $event->getRequest();
        $this->init($request);
//        $this->_app->addStyle( $this->getMisc() . '/reset.css' );
//        $this->_app->addStyle( $this->getMisc() . '/siteforever.css' );

//        if( file_exists( SF_PATH . DS . trim( $this->getCss(), '/' ) . '/style.css' ) ) {
            $this->_app->addStyle( $this->getCss() . '/style.css' );
//        }
        if( file_exists( trim( $this->getCss(), '/' ) . '/print.css' ) ) {
            $this->_app->addStyle( $this->getCss() . '/print.css' );
        }
        if( file_exists( trim( $this->getJs() . '/script.js', '/' ) ) ) {
            $this->_app->addScript( $this->getJs() . '/script.js' );
        }
//        if ( $this->_app->getAuth()->currentUser()->hasPermission(USER_ADMIN) ) {
//            $this->attachJUI();
//            $this->_app->addStyle( $this->getMisc() . '/admin/admin.css' );
//            $this->attachWysiwyg();
//            $this->_app->addScript( $this->getMisc() . '/admin/panel.js' );
//        }

        $this->getTpl()->assign('response', $event->getResponse());
        $event->getResponse()->setContent($this->getTpl()->fetch(
            $request->get('resource') . $request->getTemplate()
        ));
        return $event;
    }
}

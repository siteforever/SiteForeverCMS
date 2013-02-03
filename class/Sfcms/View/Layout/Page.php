<?php
/**
 * Формируем лэйаут для страниц
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Sfcms\View\Layout;

class Page extends Layout
{
    /**
     * @param $result
     * @return string
     */
    public function view( $result )
    {
//        $this->_app->addStyle( $this->getMisc() . '/reset.css' );
//        $this->_app->addStyle( $this->getMisc() . '/siteforever.css' );

        if( file_exists( trim( $this->getCss(), '/' ) . '/style.css' ) ) {
            $this->_app->addStyle( $this->getCss() . '/style.css' );
        }
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

        $layout = $this->getTpl()->fetch(
            $this->getRequest()->get( 'resource' )
            . $this->getRequest()->get( 'template' )
        );
        return $layout;
    }
}

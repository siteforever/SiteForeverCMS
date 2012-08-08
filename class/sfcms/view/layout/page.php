<?php
/**
 * Формируем лэйаут для страниц
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_View_Layout_Page extends Sfcms_View_Layout
{
    /**
     * @param $result
     * @return string
     */
    public function view( $result )
    {
        if( file_exists( trim( $this->getCss(), '/' ) . '/style.css' ) ) {
            $this->_app->addStyle( $this->getCss() . '/style.css' );
        }
        if( file_exists( trim( $this->getCss(), '/' ) . '/print.css' ) ) {
            $this->_app->addStyle( $this->getCss() . '/print.css' );
        }
        if( file_exists( trim( $this->getJs() . '/script.js', '/' ) ) ) {
            $this->_app->addScript( $this->getJs() . '/script.js' );
        }

        $layout = $this->getTpl()->fetch(
            $this->getRequest()->get( 'resource' )
            . $this->getRequest()->get( 'template' )
        );
        return $layout;
    }
}

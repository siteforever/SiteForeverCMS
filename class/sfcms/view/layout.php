<?php
/**
 * Представление с layout
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_View_Layout extends Sfcms_View_IView
{
    protected function init()
    {
        /** Данные шаблона */
        $this->getTpl()->assign(
            $this->getRequest()->get( 'tpldata' )
        );
        $this->getTpl()->assign( array(
            'config'   => $this->_app->getConfig(),
            'feedback' => $this->getRequest()->getFeedbackString(),
            'host'     => isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '',
            'request'  => $this->getRequest(),
        ) );
    }


    /**
     * @param $result
     * @return string
     */
    public function view( $result )
    {
        header( 'Content-type: text/html; charset=utf-8' );
        $this->init();

        if( $this->getRequest()->get( 'resource' ) == 'system:' ) {
            $output = new Sfcms_View_Layout_Admin( $this->_app );
        } else {
            $output = new Sfcms_View_Layout_Page( $this->_app );
        }
        $return = $output->view( $result );
        $return = preg_replace( '/[ \t]+/', ' ', $return );
        $return = preg_replace( '/\n[ \t]+/', "\n", $return );
        $return = preg_replace( '/\n+/', "\n", $return );

        return $return;
    }


    /**
     * @return string
     */
    protected function getCss()
    {
        return $this->getRequest()->get( 'path.css' );
    }


    /**
     * @return string
     */
    protected function getJs()
    {
        return $this->getRequest()->get( 'path.js' );
    }


    /**
     * @return string
     */
    protected function getMisc()
    {
        return $this->getRequest()->get( 'path.misc' );
    }

}

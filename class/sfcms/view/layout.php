<?php
/**
 * Представление с layout
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_View_Layout extends Sfcms_View_IView
{
//    const JQ_UI_THEME = 'redmond';
    const JQ_UI_THEME = 'flick';
    const JQ_UI_VERSION = '1.9.2';

    protected function init()
    {
        /** Данные шаблона */
        $this->getTpl()->assign( array(
            'path'     => $this->getRequest()->get('path'),
            'config'   => $this->_app->getConfig(),
            'feedback' => $this->getRequest()->getFeedbackString(),
            'host'     => isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : 'console',
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

        $return = $this->selectLayout()->view( $result );

        $return = preg_replace( '/[ \t]+/', ' ', $return );
        $return = preg_replace( '/\n[ \t]+/', "\n", $return );
        $return = preg_replace( '/\n+/', "\n", $return );

        return $return;
    }


    /**
     * Выбор лэйаута
     * @return Sfcms_View_Layout
     */
    protected function selectLayout()
    {
        if( $this->getRequest()->get( 'resource' ) == 'system:' ) {
            $result = new Sfcms_View_Layout_Admin( $this->_app );
            $this->getRequest()->set('admin', true);
        } else {
            $result = new Sfcms_View_Layout_Page( $this->_app );
            $this->getRequest()->set('admin', false);
        }
        return $result;
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


    /**
     * Attach jQueryUI plugin
     */
    protected function attachJUI()
    {
        $this->_app->addStyle( $this->getMisc().'/jquery/'.self::JQ_UI_THEME.'/jquery-ui-'.self::JQ_UI_VERSION.'.custom.css' );
        $this->_app->addScript( $this->getMisc().'/jquery/jquery-ui-'.self::JQ_UI_VERSION.'.custom.min.js' );
    }
}

<?php
/**
 * Позволяет осуществлять доступ к стилям и скриптам из каталогов, не доступных через web
 * @author keltanas <nikolay@gmail.com>
 * @link http://siteforever.ru
 */

class Siteforever_Assets
{
    private $_scripts = array();

    private $_styles = array();

    private $_assets = array();

    /**
     * Получить список файлов стилей
     * @return array
     */
    function getStyle()
    {
        return $this->_styles;
    }

    /**
     * Добавить файл стилей
     * @param  $style
     * @return void
     */
    function addStyle( $style )
    {
        $this->_styles[ $style ] = $style;
    }

    function cleanStyle()
    {
        $this->_styles = array();
    }

    function getScript()
    {
        return $this->_scripts;
    }

    function addScript($script)
    {
//        if ( '/' == $script{0} ) {
//            $script = SF_PATH . $script;
//        }
//
//        $base_script    = dirname( $script );
//
//        if ( ! isset( $this->_assets[ $base_script ] ) ) {
//            $this->_assets[ $base_script ] = md5( $base_script );
//            App::getInstance()->getLogger()->log( $this->_assets[$base_script] . '/' . basename( $script ), 'script' );
//        }

        //$this->_scripts[ $script ] = $this->_assets[$base_script] . '/' . basename( $script );
        $this->_scripts[ $script ] = $script;
    }

    function cleanScript()
    {
        $this->_scripts = array();
    }
}

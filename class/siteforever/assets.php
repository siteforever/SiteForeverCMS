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

    /**
     * Получить список файлов стилей
     * @return array
     */
    public function getStyle()
    {
        return $this->_styles;
    }

    /**
     * Добавить файл стилей
     * @param  $style
     * @return void
     */
    public function addStyle( $style )
    {
        $this->_styles[ $style ] = $style;
    }

    public function cleanStyle()
    {
        $this->_styles = array();
    }

    public function getScript()
    {
        return $this->_scripts;
    }

    public function addScript($script)
    {
        $this->_scripts[ $script ] = $script;
    }

    public function cleanScript()
    {
        $this->_scripts = array();
    }
}

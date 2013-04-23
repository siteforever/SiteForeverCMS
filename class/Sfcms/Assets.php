<?php
/**
 * Позволяет осуществлять доступ к стилям и скриптам из каталогов, не доступных через web
 * @author keltanas <nikolay@gmail.com>
 * @link http://siteforever.ru
 */

namespace Sfcms;

class Assets
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

    /**
     * @param $alias relative "/static" path in dot notation
     * @param $path absolute path to publishing *.js file
     */
    public function publishScript($alias, $path)
    {

    }

    public function addScript($script)
    {
        $this->_scripts[ $script ] = $script;
    }

    public function cleanScripts()
    {
        $this->_scripts = array();
    }
}

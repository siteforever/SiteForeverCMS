<?php
/**
 * Объект модуля приложения
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Application_Module
{
    public  $name   = '';
    public  $title  = '';
    public  $url    = '';
    public  $class  = '';

    /**
     * Настройки модуля
     * @var array
     */
    private $_settings  = array();

    /**
     * Создает модуль
     * @param string $file
     */
    function __construct( $file )
    {
        $xml    = new SimpleXMLElement( file_get_contents( $file ) );

        $this->name     = (string) $xml['name'];
        $this->title    = (string) $xml->title;
        $this->url      = (string) $xml->url;
        $this->class    = (string) $xml->class;

        foreach ( $xml->settings->param as $param ) {
            $this->_settings[ (string) $param['name'] ] = (string) $param['value'];
        }
    }

    /**
     * Вернет настройки
     * @return array
     */
    function getSettings()
    {
        return $this->_settings;
    }
}

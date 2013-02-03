<?php
namespace Sfcms\Kernel;

/**
 * Объект модуля приложения
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Module
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
     * @param string|array $module
     */
    public function __construct( $module )
    {
        if ( is_string( $module ) ) {
            $xml    = new SimpleXMLElement( file_get_contents( $module ) );
            $this->name     = (string) $xml['name'];
            $this->title    = (string) $xml->title;
            $this->url      = (string) $xml->url;
            $this->class    = (string) $xml->class;

            foreach ( $xml->settings->param as $param ) {
                $this->_settings[ (string) $param['name'] ] = (string) $param['value'];
            }
        }

        if ( is_array( $module )) {
            $this->name = $module['name'];
            if ( isset( $module['params'] ) ) {
                foreach( $module['params'] as $key => $value ) {
                    $this->_settings[ $key ] = $value;
                }
            }
        }
    }

    /**
     * Вернет настройки
     * @return array
     */
    public function getSettings()
    {
        return $this->_settings;
    }
}

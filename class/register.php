<?php
/**
 * Системный реестр
 * @author KelTanas
 */
class Register
{
    private static $instance;
    private static $values = array();
    
    /**
     * Конфиг
     * @var Config $config
     */
    private static $config;
    /**
     * Шаблонизатор
     * @var TPL_Driver $tpl
     */
    private static $tpl;
    /**
     * Рекуест
     * @var Request $request
     */
    private static $request;
    
    private function __construct()
    {
    }
    
    static function instance()
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    function get( $key )
    {
        if ( isset( $this->values[ $key ] ) ) {
            return $this->values[ $key ];
        }
        return null;
    }
    
    function set( $key, $value )
    {
        $this->values[ $key ] = $value;
    }
    
    function setConfig( Config $config )
    {
        self::$config = $config;
    }
    
    /**
     * Вернет объект конфигурации
     * @return Config
     */
    static function getConfig()
    {
        return self::$config;
    }
    
    static function setTpl( TPL_Driver $driver )
    {
        self::$tpl = $driver;
    }
    
    /**
     * Вернет объект шаблонизатора
     * @return TPL
     */
    static function getTpl()
    {
        return self::$tpl;
    }
    
    /**
     * Установит Request в реестре
     * @param Request $request
     * @return void
     */
    static function setRequest( Request $request )
    {
        self::$request = $request;
    }
    
    /**
     * Вернет объект Request
     * @return Request
     */
    static function getRequest()
    {
        return self::$request;
    }
}





<?php
class SysConfig
{
    private $config;
    
    
    
    function __construct()
    {
        $cfg_file = 'protected/config/'.CONFIG.'.php';
        if ( file_exists( $cfg_file ) ) {
            $this->config = require $cfg_file;
        }
        else {
            throw new Exception('Конфиг не найден');
        }
    }
    
    /**
     * Установить значение
     * @param $key
     * @param $val
     * @return void
     */
    function set( $key, $val )
    {
        $path = explode('.', $key);
        if ( count( $path ) == 1 ) {
            $this->config[ $key ] = $val;
        }
        else {
            $this->seti( $path, $val );
        }
    }

    /**
     * @param string $key
     * @param array $default
     * @return void
     */
    function setDefault( $key, $default )
    {
        $config = $this->get($key);
        if ( $config ) {
            $config = array_merge( $default, $config );
        }
        else {
            $config = $default;
        }
        $this->set($key, $config);
    }

    /**
     * Получить значение
     * @param $key
     * @return mixed
     */
    function get( $key )
    {
        $path = explode('.', $key);
        if ( count( $path ) == 1 ) {
            if ( isset( $this->config[ $key ] ) ) {
                return $this->config[ $key ];
            }
        }
        else {
            return $this->geti( $path );
        }
    }

    /**
     * Получить значение по алиасу
     * @param $alias
     */
    protected function geti( $path )
    {
        $data = &$this->config;
        foreach( $path as $part ) {
            $data =& $data[ $part ];
        }
        return $data;
    }

    /**
     * Установить свое значение по алиасу
     * @param $alias
     * @param $value
     */
    protected function seti( $path, $value )    
    {
        $data = &$this->config;
        foreach( $path as $part ) {
            if ( isset($data[ $part ]) ) {
                $data =& $data[ $part ];
            }
        }
        $data = $value;
    }

}
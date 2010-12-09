<?php
/**
 * Абстрактный класс драйвера шаблона
 * @author KelTanas
 */
abstract class TPL_Driver
{
    // движок шаблонизатора
    protected $engine = null;
    
    function __call( $fname, $args )
    {
        throw new Exception("Интерфейс TPL_Driver пока не поддерживает метод {$fname}");
    }
    
    abstract function assign( $params, $value = null );
    
    abstract function display( $tpl, $cache_id = null );
    abstract function fetch( $tpl, $cache_id = null );
    
    abstract function setTplDir( $dir );
    abstract function setCplDir( $dir );

    function __set( $key, $value )
    {
        $this->assign($key, $value);
    }
    
}
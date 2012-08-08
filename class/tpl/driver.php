<?php
/**
 * Абстрактный класс драйвера шаблона
 * @author KelTanas
 */
abstract class TPL_Driver extends \Sfcms\Component
{
    // движок шаблонизатора
    protected $engine = null;

    protected $_breacrumbs  = null;
    
    public function __call( $fname, $args )
    {
        throw new Exception(t("Interface TPL_Driver does not support the method {$fname}"));
    }
    
    abstract function assign( $params, $value = null );
    
    abstract function display( $tpl, $cache_id = null );
    abstract function fetch( $tpl, $cache_id = null );
    
    abstract function setTplDir( $dir );
    abstract function setCplDir( $dir );


    public function set( $key, $value )
    {
        $this->assign($key, $value);
    }

    public function get( $key )
    {
        throw new RuntimeException(t('Driver properties write only'));
    }

    /**
     * @return View_Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        if ( null === $this->_breacrumbs ) {
            $this->_breacrumbs  = new View_Breadcrumbs();
        }
        return $this->_breacrumbs;
    }
}
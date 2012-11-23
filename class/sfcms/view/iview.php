<?php
/**
 * Интерфейс внешнего представления
 * @author: keltanas <keltanas@gmail.com>
 */
abstract class Sfcms_View_IView
{
    /** @var Application_Abstract */
    protected $_app = null;

    public function __construct( Application_Abstract $app )
    {
        $this->_app = $app;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->_app->getRequest();
    }

    /**
     * @return TPL_Driver
     */
    protected function getTpl()
    {
        return $this->_app->getTpl();
    }

    /**
     * @abstract
     * @param string $result
     * @return string
     */
     public abstract function view( $result );
}

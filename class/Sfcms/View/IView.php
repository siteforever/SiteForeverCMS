<?php
/**
 * Интерфейс внешнего представления
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View;

use Sfcms\Kernel\Base as Application;
use Sfcms\Request;
use Sfcms\Tpl\Driver;

abstract class IView
{
    /** @var Application */
    protected $_app = null;

    public function __construct( Application $app )
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
     * @return Driver
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

<?php
/**
 * Хлебная крошка
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
namespace Sfcms\View\Breadcrumbs;

use Sfcms;

class Crumb
{
    private $_name  = '';
    private $_alias = '';
    private $_sep   = '';

    public function __construct($name, $alias = null, $separator = '')
    {
        $this->_name    = $name;
        $this->_sep     = $separator;
        if ( null === $alias ) {
            $this->_alias   = null;
        } else {
            $this->_alias   = $alias;
        }
    }

    public function __toString()
    {
        if ( null === $this->_alias ) {
            return $this->_name;
        }
        return  Sfcms::html()->link($this->_name, $this->_alias);
    }
}

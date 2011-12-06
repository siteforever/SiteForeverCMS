<?php
/**
 * Хлебная крошка
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
 
class View_Breadcrumbs_Crumb
{
    private $_name  = '';
    private $_alias = '';

    public function __construct( $name, $alias = null )
    {
        $this->_name    = $name;
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
        return  "<a href='{$this->_alias}'>{$this->_name}</a>";
    }
}

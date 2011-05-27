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

    public function __construct( $name, $alias )
    {
        $this->_name    = $name;
        $this->_alias   = $alias;
    }

    public function __toString()
    {
        return  "<a href='{$this->_alias}'>{$this->_name}</a>";
    }
}

<?php
/**
 * описание класса
 * @author keltanas aka Nikolay Ermin 
 * @link http://ermin.ru
 */

class Controller_Error extends Controller
{
    function indexAction()
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle( "Error" );
        $this->request->setContent( $this->tpl->fetch('error') );
//        return true;
    }
}

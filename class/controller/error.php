<?php
/**
 * Контроллер ошибки 404
 * @author keltanas aka Nikolay Ermin 
 * @link http://ermin.ru
 */

class Controller_Error extends Sfcms_Controller
{
    public function indexAction()
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle( t('Page not found') );
        $this->request->setContent( $this->tpl->fetch('error/404.tpl') );
//        return true;
    }
}

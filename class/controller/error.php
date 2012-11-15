<?php
/**
 * Контроллер ошибки 404
 * @author keltanas aka Nikolay Ermin 
 * @link http://ermin.ru
 */

class Controller_Error extends Sfcms_Controller
{
    /**
     * Обработка ошибки 404
     * @throws Sfcms_Http_Exception
     */
    public function error404Action()
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle( t('Page not found') );
        throw new Sfcms_Http_Exception($this->tpl->fetch('error/404.tpl'), 404);
    }
}

<?php
/**
 * Контроллер ошибки 404
 * @author keltanas aka Nikolay Ermin
 * @link http://ermin.ru
 */

namespace Module\System\Controller;

use Sfcms;
use Sfcms\Controller;
use Sfcms_Http_Exception;

class ErrorController extends Controller
{
    /**
     * Обработка ошибки 404
     * @throws Sfcms_Http_Exception
     */
    public function error404Action()
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle( Sfcms::i18n()->write('Page not found') );
        throw new Sfcms_Http_Exception($this->tpl->fetch('error/404.tpl'), 404);
    }
}

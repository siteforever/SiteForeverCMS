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
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    /**
     * Обработка ошибки 404
     * @throws Sfcms_Http_Exception
     */
    public function error404Action()
    {
        $this->request->setTemplate('inner');
        $this->request->setTitle($this->t('Page not found'));
        $this->tpl->getBreadcrumbs()->addPiece('/', $this->t('Home'))->addPiece(null, $this->request->getTitle());
        $response = $this->render('error/404');
        $response->setStatusCode(404);
        return $response;
    }
}

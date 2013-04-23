<?php
namespace Sfcms;

use App;
use Sfcms\Assets;
use Sfcms_Http_Exception as Exception;
use Sfcms\Kernel\KernelBase as Service;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\ApacheRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Объект запроса
 */

class Request
{
    const TYPE_ANY  = '*/*';
    const TYPE_JSON = 'json';
    const TYPE_XML  = 'xml';

    private $feedback = array();

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    private $ajaxType = self::TYPE_ANY;

    private $error = 0;

    /** @var Response */
    private $response;

    private $_content = '';
    private $_title = '';
    private $_keywords = '';
    private $_description = '';

    /**
     * Созание запроса
     */
    public function __construct()
    {
        $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $this->request->setLocale($this->app()->getConfig('language'));
        $this->set('resource', 'theme:');

        if (in_array($this->request->getMimeType(static::TYPE_JSON), $this->request->getAcceptableContentTypes())) {
            $this->request->setRequestFormat(static::TYPE_JSON, $this->request->getMimeType(static::TYPE_JSON));
        }
        if (in_array($this->request->getMimeType(static::TYPE_XML), $this->request->getAcceptableContentTypes())) {
            $this->request->setRequestFormat(static::TYPE_XML, $this->request->getMimeType(static::TYPE_XML));
        }

        $this->_assets = new Assets();

        if ($this->request->query->has('route')) {
            $this->request->query->set('route', preg_replace('/\?.*/', '', $this->request->query->get('route')));
        }

        if ($this->request->getRequestUri()) {
            $q_pos = strrpos($this->request->getRequestUri(), '?');
            $req   = trim(substr(
                $this->request->getRequestUri(),
                $q_pos + 1,
                strlen($this->request->getRequestUri())
            ), '?&');
        }

        // дополняем request не учтенными значениями
        if (isset($req) && $opt_req = explode('&', $req)) {
            foreach ($opt_req as $opt_req_item) {
                $opt_req_item = explode('=', $opt_req_item);
                if (!$this->request->query->has($opt_req_item[0]) && isset($opt_req_item[1])) {
                    $this->request->query->set($opt_req_item[0], $opt_req_item[1]);
                }
            }
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * @return Service
     */
    public function app()
    {
        return App::getInstance();
    }


    /**
     * @return string
     */
    public function getController()
    {
        return $this->get('controller');
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->set('controller', $controller);
    }


    /**
     * @return string
     */
    public function getModule()
    {
        return $this->get('module');
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->set('module', $module);
    }


    /**
     * @return string
     */
    public function getAction()
    {
        return $this->get('action', FILTER_DEFAULT, 'index');
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->set('action', $action);
    }


    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param $keywords
     */
    public function setKeywords($keywords)
    {
        $this->_keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->_keywords;
    }

    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    public function getAjax()
    {
        return $this->isAjax();
    }

    /**
     * Установить обработку аякс принудительно
     * @param bool   $ajax
     * @param string $type
     *
     * @return void
     */
    public function setAjax($ajax = false, $type = self::TYPE_ANY)
    {
//        $this->request->headers->set('Accept', $this->request->getMimeType($type));
        $this->request->setRequestFormat($type, $this->request->getMimeType($type));
        if ($ajax) {
            $this->request->headers->set('X-Requested-With', 'XMLHttpRequest');
        } else {
            $this->request->headers->set('X-Requested-With', null);
        }
    }

    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    public function isAjax()
    {
        return $this->request->isXmlHttpRequest();
    }

    /**
     * Тип запроса
     * @return string
     */
    public function getAjaxType()
    {
        return $this->request->getRequestFormat();
    }

    /**
     * Установить состояние ошибки
     * @param int $error
     *
     * @return void
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Вернуть состояние ошибки
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Установить значение
     * @param $key
     * @param $val
     *
     * @return void
     */
    public function set($key, $val)
    {
        $this->request->query->set($key, $val);
    }

    /**
     * Получить значение
     * @param     $key
     * @param int $type @deprecated
     * @param     $default
     *
     * @return mixed
     */
    public function get($key, $type = FILTER_DEFAULT, $default = null)
    {
        return $this->request->query->get($key, $default);
    }

    /**
     * Установить заголовок страницы
     * @param string $text
     */
    public function setContent($text)
    {
        $this->_content = $text;
    }

    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getContent()
    {
        return $this->getResponse() ? $this->getResponse()->getContent() : '';
    }

    /**
     * Установить контент страницы
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Установит имя шаблона для вида
     * @param  $tpl
     *
     * @return void
     */
    public function setTemplate($tpl)
    {
        $this->set('template', $tpl);
    }

    /**
     * Вернет имя текущего шаблона для вида
     * @return array|string
     */
    public function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * Добавить сообщение
     * @param $msg
     *
     * @return void
     */
    public function addFeedback($msg)
    {
        if (is_string($msg)) {
            $this->feedback[] = $msg;

            return;
        }
        if (is_array($msg)) {
            foreach ($msg as $m) {
                if (is_string($m)) {
                    $this->feedback[] = $m;
                }
            }
        }
    }

    public function getFeedback()
    {
        return $this->feedback;
    }

    public function getFeedbackString($sep = "<br />\n")
    {
        $ret = '';
        if (count($this->feedback)) {
            $ret = join($sep, $this->feedback);
        }

        return $ret;
    }

    /**
     * Установить код ошибки
     * @param Response $response
     * @param int      $error
     * @param string   $msg
     *
     * @return array
     * @deprecated
     */
    //    public function setResponseError(Response $response, $error, $msg = '')
    //    {
    //        if ( $error instanceof Exception && ! App::isTest() ) {
    //            switch ( $error->getCode() ) {
    //                case 301:
    //                case 302:
    //                case 404:
    //                    $response->setStatusCode($error->getCode());
    //                    break;
    //                case 403:
    //                    $response->setStatusCode(403);
    //                    $response->headers->set('Location', $this->app()->getRouter()->createServiceLink('users','login'));
    //                    break;
    //            }
    //            $msg = $error->getMessage();
    //            $error = $error->getCode();
    //        } else if ( $error instanceof \Exception ) {
    //            if ( App::isDebug() ) {
    //                $this->app()->getTpl()->assign('error', $error);
    //                $msg = $this->app()->getTpl()->fetch('error.error');
    //            }
    //        }
    //
    //        if ( !$msg && !$error ) {
    //            $msg = t( 'No errors' );
    //        }
    //        $this->setResponse( 'error', $error );
    //        $this->setResponse( 'msg', $msg );
    //        return $response;
    //    }


    /**
     * Добавить параметр в ответ
     * @param Response $response
     *
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Вернет респонс массивом
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Очистит все параметры запроса
     */
    public function clearAll()
    {
        unset($this->request);
        $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    }
}
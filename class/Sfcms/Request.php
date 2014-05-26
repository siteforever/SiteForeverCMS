<?php
namespace Sfcms;

use Sfcms\Kernel\AbstractKernel as Service;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Sfcms\Basket\Base as Basket;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Объект запроса
 */

class Request extends SymfonyRequest
{
    const TYPE_ANY  = '*/*';
    const TYPE_JSON = 'json';
    const TYPE_XML  = 'xml';

    private $feedback = array();

    private $error = 0;

    /** @var Response */
    private $response;

    private $title = '';
    private $keywords = '';
    private $description = '';
    private $adminScript = null;
    private $system = false;

    protected $basket;

    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        if ($this->getRequestUri()) {
            $q_pos = strrpos($this->getRequestUri(), '?');
            $req   = trim(substr($this->getRequestUri(), $q_pos + 1, strlen($this->getRequestUri())), '?&');
        }

        // дополняем request не учтенными значениями
        if (isset($req) && $opt_req = explode('&', $req)) {
            foreach ($opt_req as $opt_req_item) {
                $opt_req_item = explode('=', $opt_req_item);
                if (!$this->query->has($opt_req_item[0]) && isset($opt_req_item[1])) {
                    $this->query->set($opt_req_item[0], $opt_req_item[1]);
                }
            }
        }
    }

    /**
     * Request is system (for admins)
     * @return bool
     */
    public function isSystem()
    {
        return $this->system;
    }

    /**
     * Set request as system
     * @param $system
     */
    public function setSystem($system)
    {
        $this->system = $system;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        if (null === $this->session) {
            // Spike for very lazy session
            $this->session = \App::cms()->getContainer()->get('session');
            $this->session->start();
        }
        return $this->session;
    }

    /**
     * @return Basket
     */
    public function getBasket()
    {
        if ( null === $this->basket ) {
            $this->basket = \Sfcms_Basket_Factory::createBasket($this);
        }
        return $this->basket;
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->get('controller', $this->get('_controller', null));
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->set('controller', $controller);
    }

    /**
     * @param string $admin_script
     */
    public function setAdminScript($admin_script)
    {
        $this->adminScript = $admin_script;
    }

    /**
     * @return string
     */
    public function getAdminScript()
    {
        if (null === $this->adminScript) {
            return $this->adminScript = strtolower($this->getModule() . '/admin/' . $this->getController());
        }
        return $this->adminScript;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->get('module', $this->get('_module', null));
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
        return $this->get('action', $this->get('_action', 'index'));
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
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
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
        $this->setRequestFormat($type, $this->getMimeType($type));
        $this->headers->set('Accept', $this->getMimeType($type));
        if ($ajax) {
            $this->headers->set('X-Requested-With', 'XMLHttpRequest');
        } else {
            $this->headers->remove('X-Requested-With');
        }
    }

    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    public function isAjax()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * Тип запроса
     * @return string
     */
    public function getAjaxType()
    {
        return $this->getRequestFormat();
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
        $this->attributes->set($key, $val);
    }

    /**
     * Установить контент страницы
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Установит имя шаблона для вида
     * @param  $tpl
     *
     * @return void
     */
    public function setTemplate($tpl)
    {
        $this->set('_template', $tpl);
    }

    /**
     * Вернет имя текущего шаблона для вида
     * @return array|string
     */
    public function getTemplate()
    {
        return $this->get('_template', $this->get('template', 'index'));
    }

    /**
     * Добавить сообщение
     * @param string|array $msg
     * @param string $type
     */
    public function addFeedback($msg, $type = 'default')
    {
        if (is_string($msg)) {
            $this->getSession()->getFlashBag()->add($type, $msg);
        } elseif (is_array($msg)) {
            foreach ($msg as $m) {
                if (is_string($m)) {
                    $this->addFeedback($m, $type);
                }
            }
        }
    }

    /**
     * @param string $type
     * @return array
     */
    public function getFeedback($type = 'default')
    {
        return $this->getSession()->getFlashBag()->get($type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasFeedback($type = 'default')
    {
        return $this->getSession()->getFlashBag()->has($type);
    }

    /**
     * @param string $sep
     * @param string $type
     * @return string
     * @deprecated since 0.7, remove since 0.8
     */
    public function getFeedbackString($sep = "<br />\n", $type = 'info')
    {
        trigger_error('Will remove since 0.8', E_USER_DEPRECATED);
        $ret = '';
        $feedback = $this->getFeedback($type);
        if (count($feedback)) {
            $ret = join($sep, $feedback);
        }

        return $ret;
    }

    public function clearFeedback()
    {
        $this->feedback = array();
    }

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
}

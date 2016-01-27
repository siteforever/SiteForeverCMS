<?php
/**
 * Интерфейс контроллера
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms;

use Sfcms\Form\Form;
use Sfcms\Tpl\Driver;
use Sfcms\Router;
use Sfcms\Model;
use Sfcms\db;
use Sfcms\Basket\Base as Basket;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Module\Page\Object\Page;
use Module\User\Object\User;

use Sfcms\Data\Watcher;

/**
 * @property \App $app
 * @property Driver $tpl
 * @property \Sfcms\Auth $auth
 * @property Router $router
 * @property CacheInterface $cache
 * @property User $user
 * @property Filesystem $filesystem
 * @property i18n $i18n
 */
abstract class Controller extends ContainerAware
{
    private static $forms = array();

    /** @var array */
    protected $params;

    /** @var array|Page */
    protected $page;

    /** @var Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->params   = $this->request->get('params');
    }

    /**
     * @param $service
     *
     * @return object
     */
    public function get($service)
    {
        if ('user' == $service) {
            return $this->auth->currentUser();
        }
        return $this->container->get($service);
    }

    /**
     * @param $service
     *
     * @return object
     */
    public function __get($service)
    {
        return $this->get($service);
    }

    /**
     * @return Driver|\Smarty
     */
    public function getTpl()
    {
        return $this->get('tpl');
    }

    /**
     * @return Basket
     */
    public function getBasket()
    {
        return $this->request->getBasket();
    }

    /**
     * Правила, определяющие доступ к приложениям
     * @return array
     */
    public function access()
    {
        return array(
            USER_ADMIN    => array(
                'admin',
            ),
        );
    }

    /**
     * Возвращает настройки по умолчанию
     * @return null|array
     */
    public function defaults()
    {
    }

    /**
     * Инициализация
     * @return void
     */
    public function init()
    {
    }

    /**
     * @param $type
     * @param $message
     */
    public function addFlash($type, $message)
    {
        $this->request->getSession()->getFlashBag()->add($type, $message);
    }

    /**
     * @param $type
     * @return bool
     */
    public function hasFlash($type)
    {
        return $this->request->getSession()->getFlashBag()->has($type);
    }

    /**
     * @param $type
     * @return array
     */
    public function getFlash($type)
    {
        return $this->request->getSession()->getFlashBag()->get($type);
    }

    /**
     * Return forms errors as prepared array
     *
     * @param Form $form
     * @return array
     */
    protected function formErrorsToArray(Form $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $field => $msg) {
            $errors[$field] = str_replace('%label%', $this->t($form->getChild($field)->getLabel()), $this->t($msg));
        }
        return $errors;
    }

    /**
     * Add form errors to flash as error
     *
     * @param Form $form
     * @param string $type
     */
    protected function formErrorsToFlash(Form $form, $type = 'error')
    {
        foreach ($this->formErrorsToArray($form) as $msg) {
            $this->addFlash($type, $msg);
        }
    }

    /**
     * Вернет указанную модель, либо модель, имя которой соответствует контроллеру
     * @param string $model
     *
     * @return Model
     * @throws Exception
     */
    public function getModel($model='')
    {
        if ('' === $model) {
            if (preg_match('/Module\\(\w+)\\Controller\\(\w+)Controller/', get_class($this), $m)) {
                $model = '\\Module\\' . $m[1] . '\\Model\\' . $m[2];
            } else {
                throw new Exception(sprintf('Model not defined in class %s', get_class($this)));
            }
        }

        return $this->get('data.manager')->getModel($model);
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->get('event.dispatcher');
    }

    /**
     * Return form by name/alias
     * @param $name
     *
     * @return Form
     * @throws Exception
     */
    public function getForm($name = null)
    {
        $formServiceId = sprintf('form.%s', trim($name));
        if ($this->container->has($formServiceId)) {
            return $this->get($formServiceId);
        }
        if (!isset(self::$forms[$name])) {
            $className = 'Forms_' . $name;
            try {
                self::$forms[$name] = new $className();
            } catch (Exception $e) {
                throw new Exception('Form class ' . $className . ' not found');
            }
        }
        return self::$forms[$name];
    }

    /**
     * Вернет соединение с БД
     * @deprecated
     * @return db
     */
    public function getDB()
    {
        return $this->get('db');
    }

    /**
     * Постраничность
     * @param $count
     * @param $perpage
     * @param $link
     * @return Pager
     */
    public function paging($count, $perpage, $link, $cacheId = null)
    {
        $config = $this->container->getParameter('template');
        if (!empty($config['pager'])) {
            $pager = new Pager($count, $perpage, $link, $this->request, $config['pager'], $cacheId);
        } else {
            $pager = new Pager($count, $perpage, $link, $this->request, null, $cacheId);
        }
        return $pager;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->get('mailer');
    }

    /**
     * Отправить сообщение
     * @param        $from
     * @param        $to
     * @param        $subject
     * @param        $msg
     * @param string $mime_type
     *
     * @return int
     */
    public function sendmail($from, $to, $subject, $msg, $mime_type = 'text/plain')
    {
        $message = $this->createMessage($from, $to, $subject, $msg, $mime_type);

        return $this->sendMessage($message);
    }

    /**
     * @param        $from
     * @param        $to
     * @param        $subject
     * @param        $msg
     * @param string $mime_type
     *
     * @return \Swift_Message
     */
    public function createMessage($from, $to, $subject, $msg, $mime_type = 'text/plain')
    {
        /** @var $message \Swift_Message */
        $message = $this->getMailer()->createMessage();
        $message
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($msg, $mime_type, 'utf-8')
        ;

        return $message;
    }

    /**
     * @param \Swift_Message $message
     *
     * @return int
     */
    public function sendMessage(\Swift_Message $message)
    {
        //$this->get('logger')->info(sprintf('Send email from %s to %s', $message->getFrom(), join(', ', array_keys($message->getTo()))));
        return $this->getMailer()->send($message);
    }

    /**
     * Перенаправление на другой урл
     * @param string $url
     * @param array $params
     * @return RedirectResponse
     */
    protected function redirect( $url = '', $params = array() )
    {
        if (! preg_match( '@^http@', $url )) {
            $url = $this->router->createLink($url, $params);
        }
        return new RedirectResponse($url);
    }

    /**
     * Перезагрузить страницу на нужную
     * @param string $url
     * @param array $params
     * @param $timeout
     *
     * @return Response
     */
    protected function reload( $url = '', $params = array(), $timeout = 0 )
    {
        Watcher::instance()->performOperations();
        return $this->render('error.reload', array(
            'url' => $this->router->createLink( $url, $params ),
            'timeout' => $timeout,
        ));
    }

    /**
     * Rendering params to template
     * @param string $tpl
     * @param array $params
     * @param string $cache_id
     *
     * @return Response
     */
    protected function render($tpl, $params=array(), $cache_id = null)
    {
        $this->tpl->assign($params);
        $this->tpl->assign(
            array(
                'request'   => $this->request,
                'page'      => $this->page,
                'auth'      => $this->auth,
            )
        );

        return new Response($this->tpl->fetch($tpl, $cache_id));
    }

    /**
     * Wrapping array to json response
     * @param array $params
     * @param null  $handle
     *
     * @return JsonResponse
     */
    protected function renderJson($params=array(), $handle=null)
    {
        $response = new JsonResponse($params);
        if ($handle) {
            $response->setCallback($handle);
        }
        return $response;
    }


    /**
     * Напечатать переведенный текст
     * @param string $cat
     * @param string $text
     * @param array $params
     * @return mixed
     */
    public function t($cat, $text = '', $params = array())
    {
        return call_user_func_array(array($this->i18n,'write'), func_get_args());
    }

    /**
     * @deprecated
     * @return \App
     */
    public function app()
    {
        return $this->app;
    }
}

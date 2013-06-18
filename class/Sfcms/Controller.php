<?php
/**
 * Интерфейс контроллера
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms;

use Sfcms\Component as Component;
use Sfcms\Module as Module;
use Sfcms\Tpl\Driver;
use Module\System\Model\TemplatesModel;
use Sfcms\Config;
use Sfcms\Request;
use Sfcms\Router;
use Sfcms\Model;
use Sfcms\Exception;
use Sfcms\i18n;
use Sfcms\db;
use Sfcms\Basket\Base as Basket;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sfcms\Cache\CacheInterface;
use Module\Page\Object\Page;
use Module\User\Object\User;

use Sfcms\Data\Watcher;

/**
 * @property Driver $tpl
 */
abstract class Controller extends Component
{
    private static $forms = array();

    /** @var Driver */
    private $_tpl = null;

    /** @var array */
    protected $params;

    /** @var array|Page */
    protected $page;

    /** @var Config $config */
    protected $config;

    /** @var Request */
    protected $request;

    /** @var Router */
    protected $router;

    /** @var User */
    protected $user;

    /** @var TemplatesModel */
    protected $templates;

    /** @var CacheInterface */
    protected static $cache = null;

    /** @var \Swift_Mailer */
    protected static $mailer = null;

    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->config   = $this->app()->getConfig();
        $this->router   = $this->app()->getRouter();
        $this->user     = $this->app()->getAuth()
            ? $this->app()->getAuth()->currentUser()
            : null;
        $this->params   = $this->request->get('params');

        // Basket should be initialized to connect the JavaScript module
        $this->getBasket();

        $defaults = $this->defaults();
        if ($defaults) {
            $this->config->setDefault($defaults[0], $defaults[1]);
        }

        $pageId     = $this->request->get('pageid', 0);
        $controller = $this->request->getController();
        $action     = $this->request->getAction();

        // Define page
        $pageObj = null;
        if ($controller) {
            if ($pageId && 'index' == $action) {
                $model    = $this->getModel('Page');
                $pageObj = $model->find($pageId);
            }
        }

        if ( null !== $pageObj ) {
            // Если страница указана как объект, то в нее нельзя сохранять левые данные
            $this->request->setTemplate($pageObj->get('template'));
            $this->request->setTitle($pageObj->get('title'));
            $this->request->setDescription($pageObj->get('description'));
            $this->request->setKeywords($pageObj->get('keywords'));
            $this->tpl->getBreadcrumbs()->fromSerialize($pageObj->get('path'));
        }

        $this->page = $pageObj;

        if ($this->app()->isDebug()) {
            if ($this->page) {
                $this->log($this->page->getAttributes(), 'Page');
            }
        }

        $this->tpl->assign(
            array(
                'request'   => $request,
                'page'      => $this->page,
                'auth'      => $this->app()->getAuth(),
                'config'    => $this->config,
            )
        );

        $this->init();
    }

    /**
     * @return Driver
     */
    public function getTpl()
    {
        if ( null === $this->_tpl ) {
            $this->_tpl = $this->app()->getTpl();
            $this->_tpl->assign('this', $this);
        }
        return $this->_tpl;
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
     * Уничтожение контроллера
     */
    public function __destruct()
    {
        $this->deInit();
    }

    /**
     * Инициализация
     * @return void
     */
    public function init()
    {
    }

    /**
     * Деинициализация
     * @return void
     */
    public function deInit()
    {
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
            if (preg_match('@^Controller_(\w+)@', get_class($this), $m)) {
                $model = $m[1];
            } elseif (preg_match('/Module\\(\w+)\\Controller\\(\w+)Controller/', get_class($this), $m)) {
                $model = '\\Module\\' . $m[1] . '\\Model\\' . $m[2];
            } else {
                throw new Exception(sprintf('Model not defined in class %s', get_class($this)));
            }
        }

        return Model::getModel($model);
    }

    /**
     * Return form by name/alias
     * @param $name
     *
     * @return mixed
     * @throws Exception
     */
    public function getForm( $name )
    {
        if (!isset(self::$forms[$name])) {
            $className = 'Forms_' . $name;
            try {
                self::$forms[$name] = new $className();
            } catch (Exception $e) {
                throw new Exception('Form class ' . $className . ' not found');
            }
        }
        return self::$forms[ $name ];
    }

    /**
     * Вернет соединение с БД
     * @deprecated
     * @return db
     */
    public function getDB()
    {
        return db::getInstance();
    }

    /**
     * @return CacheInterface
     */
    public function cache()
    {
        return $this->app()->getCacheManager();
    }

    /**
     * Постраничность
     * @param $count
     * @param $perpage
     * @param $link
     * @return Pager
     */
    public function paging($count, $perpage, $link)
    {
        return new Pager($count, $perpage, $link, $this->request);
    }

    /**
     * @param Page $page
     */
    public function setPage( Page $page)
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
        if (null === self::$mailer) {
            switch ($this->config->get('mailer.transport')) {
                case 'smtp':
                    $transport = new \Swift_SmtpTransport(
                        $this->config->get('mailer.host', 'localhost'),
                        $this->config->get('mailer.port', 25),
                        $this->config->get('mailer.security')
                    );
                    $transport->setUsername($this->config->get('mailer.username'));
                    $transport->setPassword($this->config->get('mailer.password'));
                    break;
                case 'gmail':
                    // http://stackoverflow.com/a/4691183/2090796
                    $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
                    $transport->setUsername($this->config->get('mailer.username'));
                    $transport->setPassword($this->config->get('mailer.password'));
                    $transport->setAuthMode('login');
                    break;
                case 'null':
                    $transport = new \Swift_NullTransport();
                    break;
                default:
                    $transport = new \Swift_SendmailTransport();
            }
            self::$mailer = new \Swift_Mailer($transport);
        }
        return self::$mailer;
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
    public function sendmail($from, $to, $subject, $msg, $mime_type = 'plain/text')
    {
        /** @var $message \Swift_Message */
        $message = $this->getMailer()->createMessage();
        $message
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($msg, $mime_type, 'utf-8');

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
            $url = $this->app()->getRouter()->createLink($url, $params);
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
            'url' => $this->app()->getRouter()->createLink( $url, $params ),
            'timeout' => $timeout,
        ));
    }

    /**
     * Rendering params to template
     * @param string $tpl
     * @param array $params
     *
     * @return Response
     */
    protected function render($tpl, $params=array(), $cache_id = null)
    {
        $this->getTpl()->assign($params);
        return new Response($this->getTpl()->fetch($tpl, $cache_id));
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
}

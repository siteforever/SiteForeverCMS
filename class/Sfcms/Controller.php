<?php
/**
 * Интерфейс контроллера
 * @author: keltanas <keltanas@gmail.com>
 */
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
use \Symfony\Component\HttpFoundation\JsonResponse;

use Module\Page\Object\Page;
use Module\System\Object\User;

use Sfcms\Data\Watcher;

/**
 * @property Driver $tpl
 */
abstract class Sfcms_Controller extends Component
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

    public function __construct()
    {
        $this->config   = $this->app()->getConfig();
        $this->request  = $this->app()->getRequest();
        $this->router   = $this->app()->getRouter();
        $this->user     = $this->app()->getAuth()->currentUser();
        $this->params   = $this->request->get('params');

        // Basket should be initialized to connect the JavaScript module
        $this->getBasket();

        $defaults = $this->defaults();
        if ($defaults) {
            $this->config->setDefault($defaults[0], $defaults[1]);
        }


        $pageId     = $this->request->getRequest()->get('pageid', 0);
        $controller = $this->request->getController();
        $action     = $this->request->getAction();

        // Define page
        $page = null;
        if ($controller) {
            $moduleClass = Module::getModuleClass(strtolower($controller));
            if ($pageId && 'index' == $action) {
                $relField = $moduleClass::relatedField();
                $model    = $this->getModel('Page');
                $page     = $model->find("`$relField` = ? AND `controller` = ?", array($pageId, $controller));
            }
        }

        if ( null !== $page ) {
            // todo Если страница указана как объект, то в нее нельзя сохранять левые данные
            $this->request->setTemplate($page->get('template') );
//            $this->request->setContent( $page->get('content') );
            $this->request->setTitle(   $page->get('title') );
            $this->request->setDescription( $page->get('description') );
            $this->request->setKeywords( $page->get('keywords') );
            $this->tpl->getBreadcrumbs()->fromSerialize( $page->get('path') );
        }

        $this->page     = $page;

        if ( $this->app()->isDebug() ) {
//            $this->log( $this->request, 'Request' );
            if ( $this->page ) {
                $this->log( $this->page->getAttributes(), 'Page' );
            }
        }

        $this->tpl->assign(
            array(
                'request'   => $this->request,
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
        return $this->app()->getBasket();
    }

    /**
     * Правила, определяющие доступ к приложениям
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array(
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
        if ( '' === $model  ) {
            if ( preg_match('@^Controller_(\w+)@',get_class( $this ), $m ) ) {
                $model = $m[1];
            } elseif ( preg_match('/Module\\(\w+)\\Controller\\(\w+)Controller/', get_class( $this ), $m) ) {
                $model = '\\Module\\'.$m[1].'\\Model\\'.$m[2];
            } else {
                throw new Exception(sprintf('Model not defined in class %s',get_class($this)));
            }
        }
        return Model::getModel($model);
    }

    /**
     * Установка обработчика на ajax
     * @param $ajax
     * @return void
     */
    public function setAjax( $ajax = true )
    {
        $this->request->setAjax( $ajax );
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
        if ( ! isset( self::$forms[ $name ] ) ) {
            try {
                $class_name = 'Forms_'.$name;
//                $file   = str_replace(array('_','.'), DIRECTORY_SEPARATOR, $class_name ).'.php';
//                try {
//                    require_once $file;
//                } catch ( ErrorException $e ) {
//                    die('Form class '.$class_name.' not found');
//                }
                self::$forms[ $name ] = new $class_name();
            } catch ( Exception $e ) {
                throw new Exception('Form class '.$class_name.' not found');
            }
        }
        return self::$forms[ $name ];
    }

    /**
     * Вернет статус обработки ajax
     * @return boolean
     */
    public function getAjax()
    {
        return $this->app()->getRequest()->getAjax();
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
     * Постраничность
     * @param $count
     * @param $perpage
     * @param $link
     * @return Pager
     */
    public function paging( $count, $perpage, $link )
    {
        return new Pager( $count, $perpage, $link );
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
     * @return i18n
     */
    public function i18n()
    {
        return i18n::getInstance();
    }

    /**
     * Перенаправление на другой урл
     * @param string $url
     * @param array $params
     * @return bool
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
    protected function render($tpl, $params=array())
    {
        $this->getTpl()->assign($params);
        return new Response($this->getTpl()->fetch($tpl));
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

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
        if ( $defaults ) {
            $this->config->setDefault( $defaults[0], $defaults[1] );
        }

        $pageId     = $this->request->get( 'pageid', Request::INT );
        $controller = $this->request->getController();
        $action     = $this->request->getAction();

        // Define page
        $page = null;
        if ( $controller ) {
            $moduleClass = Module::getModuleClass( strtolower( $controller ) );
            if ( $pageId && 'index' == $action ) {
                $relField = $moduleClass::relatedField();
                $model = $this->getModel('Page');
                $page  = $model->find("`$relField` = ? AND `controller` = ?", array( $pageId, $controller ));
            }
        }

        if ( null !== $page ) {
            // todo Если страница указана как объект, то в нее нельзя сохранять левые данные
            $this->request->setTemplate($page->get('template') );
            $this->request->setContent( $page->get('content') );
            $this->request->setTitle(   $page->get('title') );
            $this->request->setDescription( $page->get('description') );
            $this->request->setKeywords( $page->get('keywords') );
            $this->tpl->getBreadcrumbs()->fromSerialize( $page->get('path') );
        }

        $this->page     = $page;

        if ( $this->app()->isDebug() ) {
            $this->log( $this->request->debug(), 'Request' );
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
                $file   = str_replace(array('_','.'), DIRECTORY_SEPARATOR, $class_name ).'.php';
                require_once $file;
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
        if( preg_match( '@^http@', $url ) ) {
            $this->request->set('redirect', $url);
        } else {
            $this->request->set('redirect', $this->app()->getRouter()->createLink( $url, $params ));
        }
        return true;
    }

    /**
     * Перезагрузить страницу на нужную
     * @param string $url
     * @param array $params
     * @param $timeout
     * @param $return
     */
    protected function reload( $url = '', $params = array(), $timeout = 0, $return = false )
    {
        Watcher::instance()->performOperations();

        $script = 'window.location.href = "'
                . $this->app()->getRouter()->createLink( $url, $params ) . '";';
        if ( $timeout ) {
            $script = "setTimeout( function(){ $script }, $timeout );";
        }
        $reload = '<script type="text/javascript">'.$script.'</script>';
        $this->request->set('reload', $reload);
    }
}

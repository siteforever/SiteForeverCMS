<?php
/**
 * Интерфейс контроллера
 * @author: keltanas <keltanas@gmail.com>
 */
abstract class Sfcms_Controller
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var array|Data_Object_Page
     */
    protected $page;

    /**
     * @var Sfcms_Config $config
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var router
     */
    protected $router;

    /**
     * @var TPL_Driver
     */
    protected $tpl;

    /**
     * @var Data_Object_User
     */
    protected $user;


    /**
     * @var Basket
     */
    protected $basket;

    /**
     * @var model_Templates
     */
    protected $templates;


    /**
     * @var Application_Abstract
     */
    private $app;

    private static $forms = array();

    public function __construct( Application_Abstract $app )
    {
        $this->app      = $app;
        $this->config   = $app->getConfig();
        $this->request  = $app->getRequest();
        $this->router   = $app->getRouter();
        $this->tpl      = $app->getTpl();
        $this->user     = $app->getAuth()->currentUser();
        $this->basket   = $app->getBasket();
        $this->params = $this->request->get('params');

        $id         = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );
        $controller = $this->request->get( 'controller' );

        try {
            if ( null !== $id && 'page' != $controller ) {
                $page   = $this->getModel('Page')->find(
                    array(
                         'cond'     => 'link = ? AND controller = ? AND deleted = 0',
                         'params'   => array( $id, $controller )
                    )
                );
            }
            elseif ( 'page' == $controller && $id ) {
                $page   = $this->getModel('Page')->find( $id );
            }
            else {
                throw new Exception('Page not found');
            }
        } catch ( Exception $e ) {
            $page   = null;
        }

        if ( null !== $page ) {
            if ( ! $page->get('title') ) {
                $page->set('title', $page->get('name'));
            }
            $this->page     = $page->getAttributes();
            $this->request->setTemplate($page->get('template') );
            $this->request->setContent( $page->get('content') );
            $this->request->setTitle(   $page->get('title') );
        }

        $this->request->set( 'tpldata.page', $this->page );

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
     * Уничтожение контроллера
     */
    public function __destruct()
    {
        $this->deInit();
    }

    /**
     * Приложение
     * @return Application_Abstract
     */
    public function app()
    {
        return $this->app;
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
     * @return Sfcms_Model
     */
    public function getModel($model=null)
    {
        if ( null === $model  ) {
            $model = str_replace('Controller_', '', get_class( $this ) );
        }
        return Sfcms_Model::getModel($model);
    }

    /**
     * Установка обработчика на ajax
     * @param $ajax
     * @return void
     */
    public function setAjax( $ajax = true )
    {
        App::$ajax  = $ajax;
        $this->request->setAjax( true, Request::TYPE_ANY );
    }

    /**
     * Вернет форму
     * @param $name
     * @return Form_Form
     */
    public function getForm( $name )
    {
        if ( ! isset( self::$forms[ $name ] ) ) {
            try {
                $class_name = 'forms_'.$name;
                $file   = str_replace('_', DIRECTORY_SEPARATOR, $class_name).'.php';
                require_once $file;
                self::$forms[ $name ] = new $class_name();
            } catch ( Exception $e ) {
                die('Form class '.$class_name.' not found');
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
        return App::getInstance()->getRequest()->getAjax();
    }

    /**
     * Вернет соединение с БД
     * @deprecated
     * @return db
     */
    public function getDB()
    {
        return Db::getInstance();
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
     * Index Action
     */
    abstract public function indexAction();

    /**
     * @param Data_Object_Page $page
     */
    public function setPage( Data_Object_Page $page)
    {
        $this->page = $page;
    }

    /**
     * @return Data_Object_Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Перенаправление на другой урл
     * @param string $url
     * @param array $params
     * @return void
     */
    protected function redirect( $url = '', $params = array() )
    {
        Data_Watcher::instance()->performOperations();
        if( preg_match( '@^http@', $url ) ) {
            if ( defined('TEST') && TEST ) {
                print "Location: " . $url;
                return;
            } else {
                header( "Location: " . $url );
            }
        } else {
            if ( defined('TEST') && TEST ) {
                print "Location: " . App::getInstance()->getRouter()->createLink( $url, $params );
                return;
            } else {
                header( "Location: " . App::getInstance()->getRouter()->createLink( $url, $params ) );
            }
        }
        die();
    }

    /**
     * Перезагрузить страницу на нужную
     * @param string $url
     * @param array $params
     * @return string
     */
    protected function reload( $url = '', $params = array(), $timeout = 0, $return = false )
    {
        Data_Watcher::instance()->performOperations();

        $script = 'window.location.href = "'
                . App::getInstance()->getRouter()->createLink( $url, $params ) . '";';
        if ( $timeout ) {
            $script = "setTimeout( function(){ $script }, $timeout );";
        }
        $reload = '<script type="text/javascript">'.$script.'</script>';
        if ( ( defined('TEST') && TEST ) || $return ) {
            return $reload;
        } else {
            print( $reload );
        }
    }
}

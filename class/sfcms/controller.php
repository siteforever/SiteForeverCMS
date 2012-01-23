<?php
/**
 *
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
     * @var SysConfig $config
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
     * @var Data_Object
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
        //$this->templates= $this->getModel('Templates');
        $this->basket   = $app->getBasket();
        $this->params = $this->request->get('params');

//        print "id = {$this->request->get('id')}\n";
//        print "controller = {$this->request->get('controller')}\n";
//        print "action = {$this->request->get('action')}\n";

        $id         = $this->request->get( 'id', FILTER_SANITIZE_NUMBER_INT );
        $controller = $this->request->get( 'controller' );
//        $action     = $this->request->get( 'action' );

        try {
            if (    null   !== $id
                 && 'page' != $controller
//                 && $this->app()->getRouter()->isAlias()
            ) {
                $page   = $this->getModel('Page')->find(
                    array(
                         'cond'     => 'link = ? AND controller = ? AND deleted = 0',
                         'params'   => array($id,$controller)
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

//        var_dump( $id, $controller, $action, $page );

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
    function access()
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
    function __destruct()
    {
        $this->deInit();
    }

    /**
     * Приложение
     * @return Application_Abstract
     */
    function app()
    {
        return $this->app;
    }

    /**
     * Инициализация
     * @return void
     */
    function init()
    {
    }

    /**
     * Деинициализация
     * @return void
     */
    function deInit()
    {
    }

    /**
     * Вернет указанную модель
     * @param string $model
     * @return Model
     */
    function getModel($model)
    {
        return Model::getModel($model);
    }

    /**
     * Установка обработчика на ajax
     * @param $ajax
     * @return void
     */
    function setAjax( $ajax = true )
    {
        App::$ajax  = $ajax;
        $this->request->setAjax( true, Request::TYPE_ANY );
    }

    /**
     * Вернет форму
     * @param $name
     * @return Form_Form
     */
    function getForm( $name )
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
    function getAjax()
    {
        return App::getInstance()->getRequest()->getAjax();
    }

    /**
     * Вернет соединение с БД
     * @deprecated
     * @return db
     */
    function getDB()
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
    function paging( $count, $perpage, $link )
    {
        return new Pager( $count, $perpage, $link );
    }

    abstract function indexAction();

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
}

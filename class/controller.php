<?php

class ControllerExeption extends Exception {}

/**
 * Интерфейс контроллера
 * @author keltanas aka Nikolay Ermin
 * @link http://ermin.ru
 */

abstract class Controller
{
    /**
     * @var array
     */
    protected $params;
    /**
     * @var array
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

    function __construct( Application_Abstract $app )
    {
        $this->app      = $app;

        $this->config   = App::$config;
        $this->request  = $app->getRequest();
        $this->router   = App::$router;
        $this->tpl      = $app->getTpl();
        $this->user     = $app->getAuth()->currentUser();
        //$this->templates= $this->getModel('Templates');

        $this->basket   = $app->getBasket();
        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');

        $this->params = $this->request->get('params');

        $page   = $this->getModel('Structure')->find( $this->request->get('id') );

        if ( $page ) {
            if( ! $page->title ) {
                $page->title    = $page->name;
            }
            $this->page = $page->getAttributes();
        }

        if ( $this->page ) {
            // формируем список предков страницы
            $path_array = json_decode( $this->page['path'], true );
            $parents    = array();
            if ( $path_array && is_array($path_array) ) {
                foreach( $path_array as $path ) {
                    $parents[$path['id']] = $path['id'];
                }
            }
            $this->page['parents'] = $parents;
        }

        $this->request->set('tpldata.page', $this->page);

        $this->tpl->request = $this->request;
        $this->tpl->page    = $this->page;
    }

    /**
     * @return Application_Abstract
     */
    function app()
    {
        return $this->app;
    }

    function deInit()
    {
        //$this->page->markClean();
    }

    /**
     * Вернет указанную модель
     * @param string $model
     * @return Model
     */
    function getModel($model)
    {
        return Model::getModel('model_'.$model);
    }

    /**
     * Установка обработчика на ajax
     * @return void
     */
    function setAjax( $ajax = true )
    {
        App::$ajax  = $ajax;
    }

    /**
     * Вернет форму
     * @return form_Form
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
     * @return bool
     */
    function getAjax()
    {
        return App::$ajax;
    }

    /**
     * Вернет соединение с БД
     * @return db
     */
    function getDB()
    {
        return App::$db;
    }

    /**
     * Постраничность
     * @param $count
     * @param $perpage
     * @param $link
     * @return array
     */
    function paging( $count, $perpage, $link )
    {
        return new Pager( $count, $perpage, $link );
        /*
        $pages = ceil( $count / $perpage );
        $page  = $this->request->get( 'page' );
        $page  = $page ? $page : 1;
        $p     = array();

        $link   = preg_replace('/\/page=\d+|\/page\d+/', '', $link);

        for ( $i = 1; $i <= $pages; $i++ ) {
            if ( $i == $page ) {
                $p[]    = $i;
            }
            else {
                //print href($link, array('page'=>$i));
                $p_params = array('page'=>$i);
                if ( $this->request->get('order') ) {
                    $p_params['order'] = $this->request->get('order');
                }
                $p[]    = '<a '.href($link, $p_params).'>'.$i.'</a>';
            }
        }

        $html = count($p) > 0 ? '<div class="paging">Страница: '.join(' - ',$p).'</div>' : "";
        $offset = ($page - 1) * $perpage;
        $limit = ($pages > 1) ? " LIMIT {$offset}, {$perpage}" : "";

        return array(
            'total'     => $count,
            'count'     => $count,
            'page'      => $page,
            'perpage'   => $perpage,
            'offset'    => $offset,
            'from'      => $offset,
            'to'        => $offset + $perpage,
            'html'      => $html,
            'limit'     => $limit,
        );
         */
    }

    abstract function indexAction();


}

<?php
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

    function __construct()
    {
        $this->app      = App::getInstance();

        $this->config   = App::$config;
        $this->request  = App::$request;
        $this->router   = App::$router;
        $this->tpl      = App::$tpl;
        $this->user     = App::$user;
        $this->templates= App::$templates;
        $this->basket   = App::$basket;

        $this->params = $this->request->get('params');

        $this->page = $this->getModel('Structure')->find( $this->request->get('id') );

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
            $this->page->markClean();
        }

        $theme = $this->config->get('template.theme');

        $this->request->set('tpldata', array(
            'path'  => array(
                'css'   => 'http://'.$_SERVER['HTTP_HOST'].'/themes/'.$theme.'/css',
                'js'    => 'http://'.$_SERVER['HTTP_HOST'].'/themes/'.$theme.'/js',
                'images'=> 'http://'.$_SERVER['HTTP_HOST'].'/themes/'.$theme.'/images',
                'misc'  => 'http://'.$_SERVER['HTTP_HOST'].'/misc',
            ),
            'page'   => $this->page ? $this->page->getAttributes() : null,
        ));

        $this->request->addStyle($this->request->get('tpldata.path.misc').'/reset.css');
        $this->request->addStyle($this->request->get('tpldata.path.misc').'/fancybox/jquery.fancybox-1.3.1.css');
        $this->request->addStyle($this->request->get('tpldata.path.misc').'/siteforever.css');

        if ( file_exists( 'themes/'.$theme.'/css/style.css' ) ) {
            $this->request->addStyle($this->request->get('tpldata.path.css').'/style.css');
        }

        if ( file_exists( 'themes/'.$theme.'/css/print.css' ) ) {
            $this->request->addStyle($this->request->get('tpldata.path.css').'/print.css');
        }


        $this->request->addScript($this->request->get('tpldata.path.misc').'/jquery.min.js');
        $this->request->addScript($this->request->get('tpldata.path.misc').'/etc/catalog.js');
        $this->request->addScript($this->request->get('tpldata.path.misc').'/fancybox/jquery.fancybox-1.3.1.pack.js');
        $this->request->addScript($this->request->get('tpldata.path.js').'/script.js');
    }

    /**
     * @return Application_Abstract
     */
    function app()
    {
        return $this->app;
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
        $pages = ceil( $count / $perpage );
        $page  = $this->request->get( 'page' );
        $page  = $page ? $page : 1;
        $p     = array();

        for ( $i = 1; $i <= $pages; $i++ ) {
            if ( $i == $page ) {
                $p[]    = $i;
            }
            else {
                $link   = preg_replace('/\/page=\d+|\/page\d+/', '', $link);
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
    }

    abstract function indexAction();


}

<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

use Sfcms\Assets;
use Sfcms\Controller\Resolver;

abstract class Application_Abstract
{
    static protected $instance = null;

    /**
     * @var Sfcms_Config
     */
    static $config;
    /**
     * @var TPL_Driver
     */
    static $tpl;

    /**
     * Модель для работы с шаблонами из базы
     * Центролизовать необходимо для работы из виджета
     * @var model_Templates
     */
    static $templates;
    /**
     * @var router
     */
    static $router;
    /**
     * @var db
     */
    static $db = null;
    /**
     * @var Request
     */
    static $request;

    /**
     * @var Model_Page
     */
    static $page;

    /**
     * @var Basket
     */
    static $basket;

    /**
     * @var Data_Object
     */
    static $user;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * Указывает на класс авторизации
     * @var string
     */
    protected $auth_format;

    /**
     * Класс настроек
     * @var Settings
     */
    protected $_settings;

    /**
     * Список контроллеров и их конфиги
     * @var array
     */
    protected $_controllers = null;

    /**
     * Список моделей
     * @var array
     */
    protected $_models = null;

    /**
     * Список модулей и контроллеры в них
     * @var array
     */
    public $_modules_config = array();

    /**
     * Время запуска
     * @var int
     */
    static $start_time = 0;

    /**
     * Время работы контроллера
     * @var int
     */
    static $controller_time = 0;

    /**
     * Врямя, затраченное до запуска контроллера
     * @var int
     */
    static $init_time   = 0;

    /**
     * @var std_logger
     */
    private $_logger = null;

    /**
     * Список установленных в систему модулей
     * @var array
     */
    protected $_modules = array();

    /**
     * Вернет менеджер Кэша
     * @var Sfcms_Cache
     */
    protected $_cache = null;

    /**
     * Статические скрипты и стили
     * @var \Sfcms\Assets
     */
    protected  $_assets = null;

    /** @param Resolver */
    protected $_resolver;

    /**
     * @var Sfcms\Session
     */
    protected $_session = null;

    /**
     * @var Sfcms\Delivery;
     */
    protected $_devivery = null;



    abstract public function run();

    abstract protected function init();

    abstract protected function handleRequest();

    public function __construct( $cfg_file = null )
    {
        header('X-Powered-By: SiteForeverCMS');
        App::autoloadRegister(array('App','autoload'));

        if ( is_null( self::$instance ) ) {
            self::$instance = $this;
        } else {
            throw new Application_Exception('You can not create more than one instance of Application');
        }
        // Конфигурация
        self::$config   = new Sfcms_Config( $cfg_file );
        // Загрузка параметров модулей
        $this->getControllers();
    }
    
    /**
     * @static
     * @throws Application_Exception
     * @return Application_Abstract
     */
    static public function getInstance()
    {
        if ( null === self::$instance ) {
            throw new Application_Exception('Application NOT instanced');
        }
        return self::$instance;
    }

    static public function import( $path )
    {
        $include_list    = array_reverse( explode( PATH_SEPARATOR, get_include_path() ) );
        $include_list[ ] = $path;
        set_include_path( implode( PATH_SEPARATOR, array_reverse( $include_list ) ) );
    }

    public function __set($name, $value)
    {
        $this
            ->getLogger()
            ->log( sprintf('%s = %s',$name,$value), 'app_set' );
    }

    /**
     * Установить формат авторизации
     * @param string $format
     * @return void
     */
    protected function setAuthFormat( $format )
    {
        $this->auth_format  = $format;
    }

    /**
     * Зарегистрировать колбэк автозагрузки
     * @param  $callback
     * @return void
     */
    public static function autoloadRegister( $callback )
    {
        spl_autoload_register($callback);
    }

    /**
     * Удалить колбэк автозагрузки
     * @param  $callback
     * @return void
     */
    public static function autoloadUnRegister( $callback )
    {
        spl_autoload_unregister($callback);
    }

    /**
     * Получить объект авторизации
     * @throws Exception
     * @return Auth
     */
    public function getAuth()
    {
        if ( is_null( $this->auth ) ) {
            if ( ! $this->auth_format ) {
                //throw new Exception('Auth type format not defined');
                $this->setAuthFormat('Session');
            }
            $class_name = 'Auth_'.$this->auth_format;
            $this->auth = new $class_name;
        }
        return $this->auth;
    }

    /**
     * Вернет объект кэша
     * @return Sfcms_Cache
     */
    public function getCacheManager()
    {
        if ( null === $this->_cache ) {
            $this->_cache = new Sfcms_Cache( $this );
        }
        return $this->_cache;
    }


    /**
     * @throws Application_Exception
     * @return Basket
     */
    public function getBasket()
    {
        if ( null === self::$basket ) {
            self::$basket   = Basket_Factory::createBasket( $this->getAuth()->currentUser() );
        }
        return self::$basket;
    }

    /**
     * Вернет доставку
     * @return Sfcms\Delivery
     */
    public function getDelivery()
    {
        if ( null === $this->_devivery ) {
            $this->_devivery = new Sfcms\Delivery($this->getSession(), $this->getBasket());
        }
        return $this->_devivery;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if ( is_null( self::$request ) ) {
            self::$request  = new Request();
        }
        return self::$request;
    }

    /**
     * @return TPL_Driver
     */
    public function getTpl()
    {
        if ( is_null( self::$tpl ) ) {
            self::$tpl  = Tpl_Factory::create( $this );
        }
        return self::$tpl;
    }

    /**
     * @return TPL_Driver
     */
    public function getView()
    {
        return $this->getTpl();
    }

    /**
     * @param $param
     * @return Sfcms_Config|mixed
     */
    public function getConfig( $param = null )
    {
        if ( null === $param ) {
            return self::$config;
        }
        return self::$config->get( $param );
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        if ( is_null( self::$router ) ) {
            self::$router   = new Router( $this->getRequest() );
        }
        return self::$router;
    }

    /**
     * @return std_logger
     */
    public function getLogger()
    {
        if ( null !== $this->_logger ) {
            return $this->_logger;
        }

        if ( ! App::isDebug() ) {
            $this->_logger = std_logger::getInstance( new std_logger_blank() );
            return $this->_logger;
        }

        if ( $typeLogger = $this->getConfig('logger') ) {
            switch ( $typeLogger ) {
                case 'file':
                    $this->_logger = std_logger::getInstance( new std_logger_file() );
                    break;
                case 'blank':
                    $this->_logger = std_logger::getInstance( new std_logger_blank() );
                    break;
                case 'chrome':
                    $this->_logger = std_logger::getInstance( new std_logger_chrome() );
                    break;
                case 'firephp':
                    $this->_logger = std_logger::getInstance( new std_logger_firephp() );
                    break;
                case 'plain':
                    $this->_logger = std_logger::getInstance( new std_logger_plain() );
                    break;
                case 'auto':
                    if ( isset( $_SERVER[ 'HTTP_HOST' ] ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
                        if ( false !== stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'chrome' ) ) {
                            $this->_logger = std_logger::getInstance( new std_logger_chrome() );
                        } elseif ( !(
                                false === stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'firefox' )
                             || false === stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'firephp' )
                        ) ) {
                            $this->_logger = std_logger::getInstance( new std_logger_firephp() );
                        } else {
                            $this->_logger = std_logger::getInstance( new std_logger_blank() );
                        }
                    }
                    break;
                default:
                    $this->_logger = std_logger::getInstance();
            }
        }

        return $this->_logger;
    }

    /**
     * Вернет текущего пользователя
     * @return Data_Object_User
     */
    public function getUser()
    {
        return $this->getAuth()->currentUser();
    }

    /**
     * Вернет модель
     * @param string $model
     * @return Sfcms_Model
     */
    public function getModel( $model )
    {
        return Sfcms_Model::getModel( $model );
    }

    /**
     * Настройки сайта
     * @return Settings
     */
    public function getSettings()
    {
        if (  is_null( $this->_settings ) ) {
            $this->_settings    = new Settings();
        }
        return $this->_settings;
    }


    /**
     * @return Sfcms\Controller\Resolver
     */
    public function getResolver()
    {
        if( null === $this->_resolver ) {
            $this->_resolver = new Resolver();
        }
        return $this->_resolver;
    }


    public function getAssets()
    {
        if ( null === $this->_assets ) {
            $this->_assets  = new Assets();
            $misc = $this->getRequest()->get( 'path.misc' );
    //        $this->addStyle( $misc . '/jquery/lightbox/css/jquery.lightbox-0.5.css' );
            $this->_assets->addStyle( $misc . '/jquery/fancybox/jquery.fancybox-1.3.1.css' );
            $this->_assets->addStyle( $misc . '/bootstrap/css/bootstrap.css' );

//            $this->_assets->addScript( $misc . '/jquery/jquery-1.7.2'.(App::isDebug()?'':'.min').'.js' );
    //        $this->addScript( $misc . '/jquery/lightbox/jquery.lightbox-0.5.js' );
//            $this->_assets->addScript( $misc . '/jquery/fancybox/jquery.fancybox-1.3.1.js' );
//            $this->_assets->addScript( $misc . '/siteforever.js' );
        }
        return $this->_assets;
    }

    /**
     * @return Sfcms\Session
     */
    public function getSession()
    {
        if ( null === $this->_session ) {
            $this->_session = new Sfcms\Session();
        }
        return $this->_session;
    }


    /**
     * Получить список файлов стилей
     * @return array
     */
    public function getStyle()
    {
        return $this->getAssets()->getStyle();
    }


    public function addStyle( $style )
    {
        $this->getAssets()->addStyle( $style );
    }


    public function cleanStyle()
    {
        $this->getAssets()->cleanStyle();
    }


    public function getScript()
    {
        return $this->getAssets()->getScript();
    }


    public function addScript( $script )
    {
        $this->getAssets()->addScript( $script );
    }


    public function cleanScript()
    {
        $this->getAssets()->cleanScript();
    }

    /**
     * Вернет список зарегистрированных модулей
     * @return array of Application_Module
     */
    public function getModules()
    {
        // todo от этого метода зависят классы Settings и Controller_Settings, поэтому пока вернем пустой массив
//        if ( null === $this->_modules ) $this->loadModulesConfigs();
        return $this->_modules;
    }

    /**
     * Проверит существование модуля
     */
    public function hasModule( $name )
    {
        // todo Реализодвать нормально, пока не используется
        return false;
        if ( null === $this->_modules ) $this->loadModulesConfigs();
        return array_reduce($this->_modules,function( $result, $item ) use ( $name ) {
            return $result || ( $item == $name );
        }, false);
    }


    /**
     * Загружает конфиги модулей
     * @return array
     */
    protected function loadModulesConfigs()
    {
        if ( ! $this->_modules_config ) {
            $_ = $this;
            $module_model = $this->getModel('Module\\System\\Model\\ModuleModel');
            $modules = $module_model->findAll(array('order'=>'pos'));

            /** @var $module Data_Object_Module */
            array_map(function( $module ) use ( $_ ) {
                if ( $module->active ) {
                    $mod_config = require_once $module->path.'/config.php';
                    $_->_modules_config[ $module->name ] = $mod_config;//['controllers'];
                }
            },$modules->getObjects());
        }
        return $this->_modules_config;
    }



    /**
     * @return array
     */
    public function getModels()
    {
        if ( null === $this->_models ) {
            $this->loadModulesConfigs();
            $this->_models = array_change_key_case( array_filter( array_reduce( $this->_modules_config, function($total, $current){
                return $total + array_map(function($model){
                    return is_string( $model )
                        ? $model
                        : ( is_array( $model ) && isset( $model['class'] )
                            ? $model['class'] : '' );
                },$current['models']);
            }, array() ) ) );
        }
        return $this->_models;
    }


    /**
     * Загружает список известных системе контроллеров
     * <p>Загружется список установленных модулей.</p>
     * <p>Из них формируется список контроллеров, которые имеются в системе</p>

     * @return array
     */
    public function getControllers()
    {
        if ( null === $this->_controllers ) {
            $this->loadModulesConfigs();

            $this->_controllers = array();
            foreach ( $this->_modules_config as $module => $config ) {
                foreach ( $config['controllers'] as $controller => $params ) {
                    if ( 'System' == $module ) {
                        $params['module'] = null;
                    } else {
                        $params['module'] = $module;
                    }
                    $this->_controllers[strtolower($controller)] = $params;
                }
            }
        }
        return $this->_controllers;
    }


    public function hasController( $name )
    {
        if ( null === $this->_controllers ) {
            throw new Application_Exception('Controllers list not loaded');
        }
        return isset( $this->_controllers[$name] );
    }


    /**
     * @static
     * @param string $className
     * @return boolean
     * @throws Exception
     */
    static public function autoload( $className )
    {
        static $class_count = 0;

        if ( ! preg_match('/^(Module|Archive)/', $className) ) {
            $className = strtolower( $className );
        }

        if( in_array( $className, array( 'finfo' ) ) ) {
            return false;
        }

        if( $className == 'register' ) {
            throw new Exception( 'Autoload Register class' );
        }

        // PEAR format autoload
        $className = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $className );
        $className = str_replace( '_', DIRECTORY_SEPARATOR, $className );
        $file       = $className . '.php';

        if( @include_once $file ) {
            if( defined( 'DEBUG_AUTOLOAD' ) && DEBUG_AUTOLOAD ) {
                $class_count ++;
            }
            return true;
        }
        return false;
    }
}


/**
 * Печать дампа переменной
 * @param $var
 */
function printVar( $var )
{
    print '<pre>'.print_r( $var, 1 ).'</pre>';
}

/**
 * Отправить сообщение
 * @param string $from
 * @param string $to
 * @param string $subject
 * @param string $message
 */
function sendmail( $from, $to, $subject, $message )
{
    $header = "Content-type: text/plain; charset=\"UTF-8\"\n";
    $header .= "From: {$from}\n";
    $header .= "Subject: $subject\n";
    $header .= "X-Mailer: SiteForeverCMS\n";
    $header .= "Content-type: text/plain; charset=\"UTF-8\"\n";

    return mail( $to, $subject, $message, $header );
}

/**
 * Напечатать переведенный текст
 * @param string $cat
 * @param string $text
 * @param array $params
 * @return mixed
 */
function t( $cat, $text = '', $params = array() )
{
    return call_user_func_array(array(Sfcms_i18n::getInstance(),'write'), func_get_args());
}

/**
 * Заменяет в строке $replace подстроки $search на строку $subject
 * @param $string
 * @param $h1
 * @return mixed
 */
function str_random_replace( $subject, $replace, $search = '%h1%' )
{
    return str_replace( $search, $subject, trim( array_rand( array_flip( explode( "\n", $replace ) ) ) ) );
}
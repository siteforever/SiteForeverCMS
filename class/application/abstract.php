<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

abstract class Application_Abstract
{
    static protected $instance = null;

    /**
     * Сигнализирует, как обрабатывать запрос
     * @var bool
     */
    static $ajax = false;

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
    protected $_logger = null;

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

    abstract public function run();

    abstract protected function init();

    abstract protected function handleRequest();

    public function __construct( $cfg_file = null )
    {
        App::autoloadRegister(array('App','autoload'));

        if ( is_null( self::$instance ) ) {
            self::$instance = $this;
        } else {
            throw new Application_Exception('You can not create more than one instance of Application');
        }
        // Конфигурация
        self::$config   = new Sfcms_Config( $cfg_file );

        // Загрузка параметров модулей
        $this->loadModules();
    }
    
    /**
     * @static
     * @throws Exception
     * @return Application_Abstract
     */
    static public function getInstance()
    {
        if ( is_null( self::$instance ) ) {
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
        $this->_logger->log( "$name = $value", 'app_set' );
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
            $this->_cache = new Sfcms_Cache();
        }
        return $this->_cache;
    }


    /**
     * @throws Application_Exception
     * @return Basket
     */
    public function getBasket()
    {
        if ( is_null( self::$basket ) )
        {
            self::$basket   = Basket_Factory::createBasket( $this->getAuth()->currentUser() );
        }
        return self::$basket;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if ( is_null( self::$request ) ) {
            self::$request  = new Request();
            self::$ajax     = self::$request->getAjax();
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
     * @return Sfcms_Config
     */
    public function getConfig()
    {
        return self::$config;
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
     * @return Logger_Interface
     */
    public function getLogger()
    {
        if ( null !== $this->_logger ) {
            return $this->_logger;
        }
//        return $this->_logger = std_logger::getInstance( new std_logger_plain() );

        if ( ! isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
            $this->_logger = std_logger::getInstance( new std_logger_plain() );
//            return $this->_logger;
        } else if ( isset( $_SERVER[ 'HTTP_HOST' ] ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            if ( false !== stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'chrome' ) ) {
                $this->_logger = std_logger::getInstance( new std_logger_chrome() );
            }
            elseif ( !( false === stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'firefox' )
                || false === stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'firephp' ) )
            ) {
                $this->_logger = std_logger::getInstance( new std_logger_firephp() );
            }
            else {
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
     * Загрузка конфигураций модулей
     * @return void
     */
    public function loadModules()
    {
        $files  = glob( SF_PATH.DIRECTORY_SEPARATOR.'protected'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'*.xml');

        foreach ( $files as $file ) {
            $module = new Application_Module( $file );
            $this->_modules[ $module->name ]   = $module;
        }
    }

    /**
     * Вернет список зарегистрированных модулей
     * @return array of Application_Module
     */
    public function getModules()
    {
        return $this->_modules;
    }

}

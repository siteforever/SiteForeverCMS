<?php
/**
 * Интерфейс приложения
 * User: keltanas
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
     * @var SysConfig
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
     * @var model_Structure
     */
    static $structure;

    /**
     * @var Basket
     */
    static $basket;

    /**
     * @var Data_Object
     */
    static $user;

    /**
     * @var Auth_Abstract
     */
    protected $auth;

    /**
     * Указывает на класс авторизации
     * @var string
     */
    protected $auth_format;

    /**
     * Время запуска
     * @var int
     */
    static $start_time = 0;

    /**
     * @var Logger_Interface
     */
    protected $logger;

    abstract public function run();

    abstract function init();

    abstract function handleRequest();

    function __construct( $cfg_file = null )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = $this;
        } else {
            throw new Application_Exception('You can not create more than one instance of Application');
        }
        // Конфигурация
        self::$config   = new SysConfig( $cfg_file );
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

    function __set($name, $value)
    {
        $this->logger->log( "$name = $value", 'app_set' );
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
     * Получить объект авторизации
     * @throws Exception
     * @return Auth_Abstract
     */
    function getAuth()
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
     * @throws Application_Exception
     * @return Basket
     */
    function getBasket()
    {
        if ( is_null( self::$basket ) ) {
            self::$basket   = Basket_Factory::createBasket( $this->getAuth()->currentUser() );
        }
        return self::$basket;
    }

    /**
     * @return Request
     */
    function getRequest()
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
    function getTpl()
    {
        if ( is_null( self::$tpl ) ) {
            self::$tpl  = Tpl_Factory::create();
        }
        return self::$tpl;
    }

    /**
     * @return SysConfig
     */
    function getConfig()
    {
        return self::$config;
    }

    /**
     * @return Router
     */
    function getRouter()
    {
        if ( is_null( self::$router ) ) {
            self::$router   = new Router( $this->getRequest() );
        }
        return self::$router;
    }

    /**
     * @return Logger_Interface
     */
    function getLogger()
    {
        if ( ! isset( $this->logger ) ) {
            switch ( strtolower( trim( $this->getConfig()->get('logger') ) ) ) {
                case 'firephp':
                    $this->logger   = new Logger_Firephp();
                    break;
                case 'html':
                    $this->logger   = new Logger_Html();
                    break;
                case 'plain':
                    $this->logger   = new Logger_Plain();
                    break;
                default:
                    $this->logger   = new Logger_Blank();
            }
        }
        return $this->logger;
    }


}

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

    abstract protected function init();

    abstract protected function handleRequest();

    function __construct()
    {
        self::setInstance( $this );
    }

    static protected function setInstance( Application_Abstract $app )
    {
        if ( ! is_null( self::$instance ) ) {
            throw new Exception('Application WAS instanced');
        }
        self::$instance = $app;
    }

    /**
     * @static
     * @throws Exception
     * @return Application_Abstract
     */
    static public function getInstance()
    {
        if ( is_null( self::$instance ) ) {
            throw new Exception('Application NOT instanced');
        }
        return self::$instance;
    }

    /**
     * @return Logger_Interface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    function __set($name, $value)
    {
        $this->logger->log( "$name = $value", 'app_set' );
    }

    protected function setAuthFormat( $format )
    {
        $this->auth_format  = $format;
    }

    function getAuth()
    {
        if ( is_null( $this->auth ) ) {
            if ( ! $this->auth_format ) {
                throw new Exception('Auth type format not defined');
            }
            $class_name = 'Auth_'.$this->auth_format;
            $this->auth = new $class_name();
        }
        return $this->auth;
    }
}

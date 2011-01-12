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
     * @var model_User
     */
    static $user;

    /**
     * Время запуска
     * @var int
     */
    static $start_time = 0;


    protected $logger;

    abstract function run();

    abstract function init();

    abstract function handleRequest();

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
     * @return logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}

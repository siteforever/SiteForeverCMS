<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Sfcms\Kernel;

use Sfcms\Assets;
use Sfcms\Cache\CacheInterface;
use Sfcms\Controller\Resolver;
use Sfcms\Model;
use Sfcms\Module;
//use Sfcms\Session;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Sfcms\Settings;
use Sfcms\Delivery;
use Sfcms\Config;
use Sfcms\Request;
use Sfcms\Router;
use Sfcms\i18n;
use Sfcms\db;
use Sfcms\Tpl\Factory;
use Sfcms\Tpl\Driver;
use Module\System\Model\TemplatesModel;
use Module\Page\Model\PageModel;

use Sfcms\Data\Object;
use Module\User\Object\User;
use Sfcms_Basket_Factory;
use Sfcms_Cache;

use Sfcms\Basket\Base as Basket;

use Std_Logger;
use Auth;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class KernelBase
{
    static protected $instance = null;

    /**
     * @var Config
     */
    static $config;
    /**
     * @var Driver
     */
    static $tpl;

    /**
     * Модель для работы с шаблонами из базы
     * Центролизовать необходимо для работы из виджета
     * @var TemplatesModel
     */
    static $templates;
    /**
     * @var Router
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
     * @var PageModel
     */
    static $page;

    /**
     * @var Basket
     */
    static $basket;

    /**
     * @var Object
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
     * @var Std_Logger
     */
    private $_logger = null;

    /**
     * Список установленных в систему модулей
     * @var array
     */
    private $_modules = array();

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
     * @var Session
     */
    protected $_session = null;

    /**
     * @var Delivery;
     */
    protected $_devivery = null;

    /**
     * @var EventDispatcher
     */
    protected $_event_dispatcher = null;



    abstract public function run();

    abstract protected function init();

    abstract protected function handleRequest();

    public function __construct($cfg_file)
    {
//        self::autoloadRegister(array($this,'autoload'));

        if ( is_null( self::$instance ) ) {
            self::$instance = $this;
        } else {
            throw new Exception('You can not create more than one instance of Application');
        }
        // Конфигурация
        self::$config   = new Config( $cfg_file );
        // Загрузка параметров модулей
        $this->getControllers();
    }

    /**
     * Защита от ошибок сериализации.
     * Иногда возникают во время тестов.
     * @return array
     */
    public function __sleep()
    {
        return array();
    }

    /**
     * @static
     * @throws Exception
     * @return KernelBase
     */
    static public function getInstance()
    {
        if ( null === self::$instance ) {
            throw new Exception('Application NOT instanced');
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
            ->getLogger()->log(sprintf('%s = %s', $name, $value), 'app_set');
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
     *
     * @return CacheInterface
     * @throws Exception
     */
    public function getCacheManager()
    {
        if ( null === $this->_cache ) {
            if ($config = $this->getConfig('cache')) {
                switch ($config['type']) {
                    case 'file':
                        $this->_cache = new \Sfcms\Cache\CacheFile($config['livecycle']);
                        break;
                    case 'apc':
                        if (!function_exists('apc_cache_info')) {
                            throw new Exception('Module APC is not active');
                        }
                        $this->_cache = new \Sfcms\Cache\CacheApc($config['livecycle']);
                        break;
                }
            }
            if (null === $this->_cache) {
                $this->_cache = new \Sfcms\Cache\CacheBlank(0);
            }
        }
        return $this->_cache;
    }


    /**
     * @return Basket
     */
    public function getBasket()
    {
        if ( null === self::$basket ) {
            self::$basket = Sfcms_Basket_Factory::createBasket($this->getAuth()->currentUser());
        }
        return self::$basket;
    }

    /**
     * Вернет доставку
     * @return Delivery
     */
    public function getDelivery()
    {
        if ( null === $this->_devivery ) {
            $this->_devivery = new Delivery($this->getSession(), $this->getBasket());
        }
        return $this->_devivery;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if ( is_null( self::$request ) ) {
            self::$request  = Request::createFromGlobals();
            $acceptableContentTypes = self::$request->getAcceptableContentTypes();
            $format = null;
            if ($acceptableContentTypes) {
                $format = self::$request->getFormat($acceptableContentTypes[0]);
            }
            self::$request->setRequestFormat($format);
            self::$request->setApp($this);
        }
        return self::$request;
    }

    /**
     * @return Driver
     */
    public function getTpl()
    {
        if ( is_null( self::$tpl ) ) {
            $config = $this->getConfig('template');

            if ( ! $config ) {
                throw new Exception('Config for templates not defined');
            }
            $theme  = $config['theme'];

            self::$tpl  = Factory::create($config);
            $themeCat = ROOT."/themes/{$theme}/templates";

            self::$tpl->setTplDir(array());
            if (is_dir($themeCat)) {
                self::$tpl->addTplDir($themeCat);
            } else {
                throw new Exception('Theme "' . $theme . '" not found');
            }
            if (is_dir(SF_PATH."/themes/system")) {
                self::$tpl->addTplDir(SF_PATH."/themes/system");
            }
            $runtime    = ROOT."/runtime";
            $tpl_c  = $runtime."/templates_c";
            $cache  = $runtime."/cache";

            if (!is_dir($tpl_c)) {
                @mkdir($tpl_c, 0755, true);
            }
            if (!is_dir($cache)) {
                @mkdir($cache, 0755, true);
            }
            self::$tpl->setCplDir($tpl_c);
            self::$tpl->setCacheDir($cache);

            self::$tpl->setWidgetsDir(SF_PATH . '/widgets');
            if (ROOT != SF_PATH) {
                self::$tpl->setWidgetsDir(ROOT . '/widgets');
            }
        }
        return self::$tpl;
    }

    /**
     * @return Driver
     */
    public function getView()
    {
        return $this->getTpl();
    }

    /**
     * @param $param
     * @return Config|string|mixed
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
     * @return Std_Logger
     */
    public function getLogger()
    {
        if ( null !== $this->_logger ) {
            return $this->_logger;
        }

        if ( ! static::isDebug() ) {
            $this->_logger = Std_Logger::getInstance( new \Std_Logger_Blank() );
            return $this->_logger;
        }

        if ( $typeLogger = $this->getConfig('logger') ) {
            switch ( $typeLogger ) {
                case 'file':
                    $this->_logger = Std_Logger::getInstance( new \Std_Logger_File() );
                    break;
                case 'blank':
                    $this->_logger = Std_Logger::getInstance( new \Std_Logger_Blank() );
                    break;
                case 'chrome':
                    $this->_logger = Std_Logger::getInstance( new \Std_Logger_Chrome() );
                    break;
                case 'firephp':
                    $this->_logger = Std_Logger::getInstance( new \Std_Logger_Firephp() );
                    break;
                case 'plain':
                    $this->_logger = Std_Logger::getInstance( new \Std_Logger_Plain() );
                    break;
                case 'auto':
                    if ( isset( $_SERVER[ 'HTTP_HOST' ] ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
                        if ( false !== stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'chrome' ) ) {
                            $this->_logger = Std_Logger::getInstance( new \Std_Logger_Chrome() );
                        } elseif ( !(
                                false === stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'firefox' )
                             || false === stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'firephp' )
                        ) ) {
                            $this->_logger = Std_Logger::getInstance( new \Std_Logger_Firephp() );
                        } else {
                            $this->_logger = Std_Logger::getInstance( new \Std_Logger_Blank() );
                        }
                    }
                    break;
                default:
                    $this->_logger = Std_Logger::getInstance();
            }
        }

        return $this->_logger;
    }

    /**
     * Вернет текущего пользователя
     * @return User
     */
    public function getUser()
    {
        return $this->getAuth()->currentUser();
    }

    /**
     * Вернет модель
     * @param string $model
     * @return Model
     */
    public function getModel( $model )
    {
        return Model::getModel( $model );
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
     * @return Resolver
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
//            $this->addStyle( '/misc/jquery/lightbox/css/jquery.lightbox-0.5.css' );
            $this->_assets->addStyle('/misc/jquery/fancybox/jquery.fancybox-1.3.1.css');
            if (!$this->getConfig('misc.noBootstrap')) {
                $this->_assets->addStyle('/misc/bootstrap/css/bootstrap.css');
            }

//            $this->_assets->addScript( $misc . '/jquery/jquery-1.7.2'.(App::isDebug()?'':'.min').'.js' );
//            $this->addScript( $misc . '/jquery/lightbox/jquery.lightbox-0.5.js' );
//            $this->_assets->addScript( $misc . '/jquery/fancybox/jquery.fancybox-1.3.1.js' );
//            $this->_assets->addScript( $misc . '/siteforever.js' );
        }
        return $this->_assets;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        if ( null === $this->_session ) {
            if (defined('TEST')) {
                $storage = new MockArraySessionStorage();
            } else {
                $model = $this->getModel('Session');
                $storage = new NativeSessionStorage(
                    array(),
                    new PdoSessionHandler($model->getDB()->getResource(), array('db_table'=>$model->getTable()))
                );
            }
            $this->_session = new Session($storage);
            $this->_session->start();
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
        $this->getAssets()->cleanScripts();
    }

    /**
     * Вернет список зарегистрированных модулей
     * @return array of Application_Module
     */
    public function getModules()
    {
        // todo от этого метода зависят классы Settings и Controller_Settings, поэтому пока вернем пустой массив
        return $this->_modules;
    }

    /**
     * Проверит существование модуля
     */
    public function hasModule( $name )
    {
        // todo Реализодвать нормально, пока не используется
        if ( null === $this->_modules ) {
            $this->loadModulesConfigs();
        }
        return array_reduce($this->_modules,function( $result, $item ) use ( $name ) {
            return $result || ( $item == $name );
        }, false);
    }

    public function setModule($name, Module $module)
    {
        $this->_modules[$name] = $module;
    }

    /**
     * Загружает конфиги модулей
     * @return array
     * @throws Exception
     */
    protected function loadModulesConfigs()
    {
        if (!$this->_modules_config) {
            $_ = $this;

            $modules = self::getConfig('modules');

            try {
                array_map(
                    function ($module) use ($_) {
                        if (!isset($module['path'])) {
                            throw new Exception('Directive "path" not defined in modules config');
                        }
                        if (!isset($module['name'])) {
                            throw new Exception('Directive "name" not defined in modules config');
                        }
                        $class = $module['path'] . '\Module';
                        $reflection = new \ReflectionClass($class);
                        $place = dirname($reflection->getFileName());
                        if (is_dir($place.'/View')) {
                            $_->getTpl()->addTplDir($place.'/View');
                        }
                        $_->setModule($module['name'], new $class($_, $module['name'], $module['path']));
                    },
                    $modules
                );
            } catch (\Exception $e) {
                die($e->getMessage());
            }

            // Сперва загрузим все конфиги
            array_map(
                function (Module $module) use ($_) {
                    $_->_modules_config[$module->getName()] = $module->config();
                },
                $this->_modules
            );

            // А потом инициализируем
            // Т.к. для инициализации могут потребоваться зависимые модули
            array_map(
                function ($module) use ($_) {
                    return method_exists($module, 'init') ? call_user_func(array($module, 'init')) : false;
                },
                $this->_modules
            );
        }

        return $this->_modules_config;
    }


    /**
     * @param $name
     *
     * @return Module
     * @throws Exception
     */
    public function getModule( $name )
    {
        if ( null === $this->_modules ) {
            $this->loadModulesConfigs();
        }
        if ( ! isset( $this->_modules[$name] ) ) {
            throw new Exception(sprintf('Module "%s" not defined', $name));
        }
        return $this->_modules[$name];
    }

    /**
     * Get array for creating menu from modules in admin panel
     * @return mixed
     */
    public function adminMenuModules()
    {
        return array_reduce( $this->getModules(), function( $total, $module ){
            return null === $module->admin_menu() ? $total : array_merge_recursive( $total, $module->admin_menu() );
        }, array() );
    }

    /**
     * @return array
     */
    public function getModels()
    {
        if ( null === $this->_models ) {
            $this->loadModulesConfigs();
            $this->_models = array_change_key_case(
                array_filter(
                    array_reduce(
                        $this->_modules_config,
                        function ($total, $current) {
                            return isset($current['models']) ? $total + array_map(
                                function ($model) {
                                    return is_string($model)
                                        ? $model : (is_array($model) && isset($model['class']) ? $model['class'] : '');
                                },
                                $current['models']
                            ) : $total;
                        }, array())));
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
                if (isset($config['controllers'])) {
                    foreach ( $config['controllers'] as $controller => $params ) {
                        $params['module'] = $module;
                        $this->_controllers[strtolower($controller)] = $params;
                    }
                }
            }
        }
        return $this->_controllers;
    }


    public function hasController( $name )
    {
        if ( null === $this->_controllers ) {
            throw new Exception('Controllers list not loaded');
        }
        return isset( $this->_controllers[$name] );
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        if ( null === $this->_event_dispatcher ) {
            $this->_event_dispatcher = new EventDispatcher();
        }
        return $this->_event_dispatcher;
    }

    /**
     * Run under development environment
     * @static
     * @return bool
     */
    static public function isDebug()
    {
        return self::getInstance()->getConfig('debug.profiler');
    }



}


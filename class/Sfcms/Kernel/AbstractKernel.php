<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Sfcms\Kernel;

use Sfcms\Assets;
use Sfcms\Cache\CacheInterface;
use Sfcms\Config;
use Sfcms\Controller\Resolver;
use Sfcms\LoggerInterface;
use Sfcms\Model;
use Sfcms\Module;
use Sfcms\Delivery;
use Sfcms\Request;
use Symfony\Component\Routing\Router as SfRouter;
use Sfcms\Router;
use Sfcms\i18n;
use Sfcms\Tpl\Directory;
use Sfcms\Tpl\Driver;
use Module\Page\Model\PageModel;

use Sfcms\Data\Object;
use RuntimeException;

use Sfcms\Basket\Base as Basket;

use Auth;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader as YamlUrlFileLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Config\FileLocator;

abstract class AbstractKernel
{
    static protected $instance = null;

    /**
     * @var PageModel
     */
    static $page;

    /**
     * @var Basket
     */
    static $basket;

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
     * Список установленных в систему модулей
     * @var array
     */
    private $_modules = array();

    /**
     * Статические скрипты и стили
     * @var \Sfcms\Assets
     */
    protected  $_assets = null;


    /** @param Resolver */
    protected $_resolver;

    /**
     * @var Delivery;
     */
    protected $_devivery = null;

    private static $_debug = null;

    protected $_is_console = false;

    /** @var ContainerBuilder */
    protected $_container = null;


    abstract public function run();

    abstract protected function init();

    abstract public function handleRequest(Request $request);


    public function __construct($cfg_file, $debug = false)
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = $this;
        } else {
            throw new Exception('You can not create more than one instance of Application');
        }

        self::$_debug = $debug;
        if ($this->isDebug()) {
            Debug::enable(E_ALL, true);
        }

        $this->_container = new ContainerBuilder();
        $this->getContainer()->set('app', $this);
        $this->getContainer()->setParameter('root', ROOT);
        $locator = new FileLocator(array(ROOT, SF_PATH));
        $loader = new YamlFileLoader($this->getContainer(), $locator);
        $loader->load('app/services.yml');

        // Конфигурация
        $config = new Config($locator->locate($cfg_file), $this->_container);
        $this->_container->set('config', $config);

        $router = new SfRouter(
            new YamlUrlFileLoader($locator),
            'app/routes.yml',
            array(/*'cache_dir' => $this->getContainer()->getParameter('cache_dir'),*/
                  'debug' => $debug)
        );
        $this->getContainer()->set('sf_router', $router);

        // Загрузка параметров модулей
        $this->loadModules();
    }


    /**
     * Загружает конфиги модулей
     * @return array
     * @throws Exception
     */
    protected function loadModules()
    {
        if (!$this->_modules_config) {
            $_ = $this;

            $moduleArray = $this->getConfig('modules');

            try {
                array_map(function ($module) use ($_) {
                    if (!isset($module['path'])) {
                        throw new Exception('Directive "path" not defined in modules config');
                    }
                    if (!isset($module['name'])) {
                        throw new Exception('Directive "name" not defined in modules config');
                    }
                    $className = $module['path'] . '\Module';
                    $reflection = new \ReflectionClass($className);
                    $place = dirname($reflection->getFileName());
                    $_->setModule(new $className($_, $module['name'], $module['path'], $place));
                }, $moduleArray);
            } catch (\Exception $e) {
                throw $e;
            }

            // Сперва загрузим все конфиги
            array_map(
                function (Module $module) use ($_) {
                    $_->_modules_config[$module->getName()] = $module->config();
                },
                $this->getModules()
            );

            // А потом инициализируем
            // Т.к. для инициализации могут потребоваться зависимые модули
            array_map(function ($module) use ($_) {
                call_user_func(array($module, 'registerService'), $_->getContainer());
                call_user_func(array($module, 'registerViewsPath'), $_->getContainer()->get('tpl_directory'));
                call_user_func(array($module, 'registerRoutes'), $_->getContainer()->get('sf_router'));
                if (method_exists($module, 'init')) {
                    call_user_func(array($module, 'init'));
                }
            },$this->getModules());
        }

        return $this->_modules_config;
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
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * @static
     * @throws Exception
     * @return AbstractKernel
     */
    static public function cms()
    {
        if ( null === self::$instance ) {
            throw new Exception('Application NOT instanced');
        }
        return self::$instance;
    }

    /**
     * Получить объект авторизации
     * @throws Exception
     * @return Auth
     */
    public function getAuth()
    {
        return $this->getContainer()->get('auth');
    }

    /**
     * Вернет объект кэша
     *
     * @return CacheInterface
     * @throws Exception
     */
    public function getCacheManager()
    {
        $this->getContainer()->get('cache');
    }

    /**
     * Вернет доставку
     * @param Request $request
     * @return Delivery
     */
    public function getDelivery(Request $request)
    {
        if ( null === $this->_devivery ) {
            $this->_devivery = new Delivery($request->getSession(), $request->getBasket());
        }
        return $this->_devivery;
    }

    /**
     * Return template driver
     * @return Driver
     * @throws Exception
     */
    public function getTpl()
    {
        return $this->getContainer()->get('tpl');
    }

    /**
     * @param $param
     * @return Config|mixed
     */
    public function getConfig( $param = null )
    {
        return (null === $param)
            ? $this->getContainer()->get('config')
            : $this->getConfig()->get($param);
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->getContainer()->get('router');
//        return $this->getContainer()->get('sf_router');
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * Вернет модель
     * @param string $model
     * @return Model
     */
    public function getModel($model)
    {
        return Model::getModel($model);
    }

    /**
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->getContainer()->get('resolver');
    }


    /**
     * @return Assets
     */
    public function getAssets()
    {
        return $this->getContainer()->get('assets');
    }


    /**
     * @return Session
     * @throws RuntimeException
     * @deprecated Session storaged in request
     */
    public function getSession()
    {
        throw new RuntimeException('Session storaged in request');
    }


    /**
     * Получить список файлов стилей
     * @return array
     * @deprecated
     */
    public function getStyle()
    {
        return $this->getAssets()->getStyle();
    }

    /**
     * @param $style
     * @deprecated
     */
    public function addStyle( $style )
    {
        $this->getAssets()->addStyle($style);
    }

    /**
     * @deprecated
     */
    public function cleanStyle()
    {
        $this->getAssets()->cleanStyle();
    }

    /**
     * @deprecated
     */
    public function getScript()
    {
        return $this->getAssets()->getScript();
    }

    /**
     * @deprecated
     */
    public function addScript( $script )
    {
        $this->getAssets()->addScript( $script );
    }

    /**
     * @deprecated
     */
    public function cleanScript()
    {
        $this->getAssets()->cleanScripts();
    }

    /**
     * Вернет список зарегистрированных модулей
     * @return array of Module
     */
    public function getModules()
    {
        return $this->_modules;
    }

    /**
     * Проверит существование модуля
     */
    public function hasModule($name)
    {
        /** @var Module $module */
        foreach ($this->getModules() as $i => $module) {
            if ($module->getName() == $name) {
                return $i;
            }
        }
        return false;
    }

    public function setModule(Module $module)
    {
        if (!in_array($module, $this->_modules, true)) {
            $this->_modules[] = $module;
            return true;
        }
        return false;
    }

    /**
     * @param $name
     *
     * @return Module
     * @throws Exception
     */
    public function getModule($name)
    {
        if ( null === $this->_modules ) {
            $this->loadModules();
        }
        /** @var Module $module */
        foreach ($this->getModules() as $module) {
            if ($module->getName() == $name) {
                return $module;
            }
        }

        throw new Exception(sprintf('Module "%s" not defined', $name));
    }

    /**
     * Get array for creating menu from modules in admin panel
     * @return mixed
     */
    public function adminMenuModules()
    {
        return array_reduce( $this->getModules(), function( $total, Module $module ){
            return null === $module->admin_menu() ? $total : array_merge_recursive( $total, $module->admin_menu() );
        }, array() );
    }

    /**
     * @return array
     */
    public function getModels()
    {
        if ( null === $this->_models ) {
            $this->loadModules();
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
            $this->loadModules();

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
        return $this->getContainer()->get('EventDispatcher');
    }


    /**
     * Run under development environment
     * @static
     * @return bool
     */
    static public function isDebug()
    {
        return self::$_debug;
    }


    /**
     * @param boolean $is_console
     * @return boolean
     */
    public function isConsole($is_console = null)
    {
        if (null === $is_console) {
            return $this->_is_console;
        }
        $this->_is_console = $is_console;
        return $is_console;
    }
}


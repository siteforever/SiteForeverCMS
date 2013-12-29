<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Sfcms\Kernel;

use Module\Market\Object\Order;
use Sfcms\Assets;
use Sfcms\Cache\CacheInterface;
use Sfcms\Config;
use Sfcms\Controller\Resolver;
use Sfcms\LoggerInterface;
use Sfcms\Model;
use Sfcms\Module;
use Sfcms\DeliveryManager;
use Sfcms\Request;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Sfcms\Router;
use Sfcms\Auth;
use Sfcms\Tpl\Driver;
use Module\Page\Model\PageModel;

use Sfcms\Basket\Base as Basket;

use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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
    private $_modules_config = array();

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
     * @var DeliveryManager;
     */
    protected $_devivery = null;

    private static $_debug = null;

    protected $_is_console = false;

    /** @var ContainerBuilder */
    protected $_container = null;

    private $environment;

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    abstract public function run();

    abstract protected function init();

    abstract public function handleRequest(Request $request);

    public function __construct($env, $debug = false)
    {
        if (is_null(static::$instance)) {
            static::$instance = $this;
        } else {
            throw new Exception('You can not create more than one instance of Application');
        }

        $this->environment = $env;

        self::$_debug = $debug;
        if ($this->isDebug()) {
            Debug::enable(E_ALL, true);
        }

        $modules = require ROOT . '/app/modules.php';
        $this->loadModules($modules);

        $cacheFile = SF_PATH . '/runtime/cache/container.php';
        $containerConfigCache = new ConfigCache($cacheFile, $this->isDebug());

        if (!$containerConfigCache->isFresh()) {
            $this->_container = $this->createNewContainer();
            $dumper = new PhpDumper($this->getContainer());
            file_put_contents($cacheFile, $dumper->dump());
        }
        require_once $cacheFile;
        $this->_container = new \ProjectServiceContainer();

        $this->getContainer()->set('app', $this);

        $this->initModules();
    }

    public function createNewContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'root' => ROOT,
            'sf_path' => SF_PATH,
            'debug' => $this->isDebug(),
            'env' => $this->getEnvironment(),
        )));

        /** @var Module $module */
        foreach($this->getModules() as $module) {
            $module->loadExtensions($container);
        }

        $locator = new FileLocator(array($container->getParameter('root'), $container->getParameter('sf_path')));
        $loader = new YamlFileLoader($container, $locator);
        $loader->load(sprintf('app/config_%s.yml', $this->getEnvironment()));

        /** @var Module $module */
        foreach($this->getModules() as $module) {
            $module->build($container);
        }

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(
            new MergeExtensionConfigurationPass(array_map(function($ext){ return $ext->getAlias(); }, $container->getExtensions()))
        );

        $container->compile();
        return $container;
    }


    /**
     * @param array $modules
     * @return array
     * @throws \Exception
     */
    protected function loadModules(array $modules)
    {
        if (!$this->_modules) {
            $_ = $this;

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
                }, $modules);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        return $this->_modules;
    }

    /**
     * Загрузка параметров модулей
     * @return array
     */
    protected function initModules()
    {
        foreach ($this->getModules() as $module) {
            call_user_func(array($module, 'registerViewsPath'), $this->getContainer()->get('tpl_directory'));
            call_user_func(array($module, 'registerRoutes'), $this->getContainer()->get('sf_router'));
            call_user_func(array($module, 'registerStatic'));
        }
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
     * @param Order $order
     * @return DeliveryManager
     */
    public function getDeliveryManager(Request $request, Order $order)
    {
        if (null === $this->_devivery) {
            $this->_devivery = new DeliveryManager($request, $order, $this->getEventDispatcher());
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
        throw new \RuntimeException('"Config" is depreceted');
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

    /**
     * @param Module $module
     * @return bool
     * @throws \RuntimeException
     */
    public function setModule(Module $module)
    {
        if (!isset($this->_modules[$module->getName()])) {
            $this->_modules[$module->getName()] = $module;
            $this->_modules_config[$module->getName()] = $module->config();
            return true;
        }
        throw new \RuntimeException(sprintf('Module "%s" was loaded', $module->getName()));
    }

    /**
     * @param $name
     *
     * @return Module
     * @throws \RuntimeException
     */
    public function getModule($name)
    {
        if (isset($this->_modules[$name])) {
            return $this->_modules[$name];
        }

        throw new \RuntimeException(sprintf('Module "%s" not defined', $name));
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
        $modulesConfig = $this->_modules_config;
        if (null === $this->_models) {
            $this->_models = array_change_key_case(
                array_filter(
                    array_reduce(
                        $modulesConfig,
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
        if (null === $this->_controllers) {
            $this->_controllers = array();
            foreach ($this->_modules_config as $module => $config) {
                if (isset($config['controllers'])) {
                    foreach ($config['controllers'] as $controller => $params) {
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
        return $this->getContainer()->get('event.dispatcher');
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


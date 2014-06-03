<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Sfcms\Kernel;

use App;
use Module\Market\Object\Order;
use Sfcms\Assets;
use Sfcms\Cache\CacheInterface;
use Sfcms\Controller\Resolver;
use Sfcms\LoggerInterface;
use Sfcms\Model;
use Sfcms\Module;
use Sfcms\DeliveryManager;
use Sfcms\Request;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Sfcms\Router;
use Sfcms\Auth;
use Sfcms\Tpl\Driver;

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
     * @var Basket
     */
    static $basket;

    /**
     * Список контроллеров и их конфиги
     * @var array
     */
    protected $_controllers = null;

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

    abstract public function run(Request $request = null);

    abstract public function handleRequest(Request $request);

    abstract public function getContainerCacheFile();

    abstract public function getLogsPath();

    abstract public function getCachePath();

    public function __construct($env, $debug = false)
    {
        $this->environment = $env;
        self::$_debug = $debug;
        if ($this->isDebug()) {
            Debug::enable(E_ALL, true);
        }

        if (is_null(static::$instance)) {
            static::$instance = $this;
        } else {
            throw new Exception('You can not create more than one instance of Application');
        }

        if (!is_dir($this->getLogsPath())) {
            @mkdir($this->getLogsPath(), 0777, true);
        }
        if (!is_dir($this->getCachePath())) {
            @mkdir($this->getCachePath(), 0777, true);
        }

        $locator = new FileLocator(array(ROOT, SF_PATH));
        $modules = require $locator->locate('app/modules.php');
        $this->loadModules($modules);

        $containerConfigCache = new ConfigCache($this->getContainerCacheFile(), $this->isDebug());

        if (!$containerConfigCache->isFresh()) {
            $this->_container = $this->createNewContainer();
            $dumper = new PhpDumper($this->getContainer());
            $containerConfigCache->write($dumper->dump());
        }
        require_once $this->getContainerCacheFile();
        $this->_container = new \ProjectServiceContainer();

        $this->getContainer()->set('app', $this);

        $this->initModules();
    }

    /**
     * @return ContainerBuilder
     */
    public function createNewContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'root' => ROOT,
            'sfcms.root' => ROOT,
            'sf_path' => SF_PATH,
            'sfcms.path' => SF_PATH,
            'debug' => $this->isDebug(),
            'env' => $this->getEnvironment(),
            'sfcms.env' => $this->getEnvironment(),
            'sfcms.cache_dir' => $this->getCachePath(),
            'sfcms.charset' => 'utf8',
        )));

        /** @var Module $module */
        foreach($this->getModules() as $module) {
            $module->loadExtensions($container);
            $module->build($container);
        }

        $locator = new FileLocator(array($container->getParameter('root'), $container->getParameter('sf_path')));
        $loader = new YamlFileLoader($container, $locator);
        $loader->load(sprintf('app/config_%s.yml', $this->getEnvironment()));
        $container->set('app', $this);

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
            try {
                array_map(function ($module) {
                    if (!isset($module['path'])) {
                        throw new Exception('Directive "path" not defined in modules config');
                    }
                    if (!isset($module['name'])) {
                        throw new Exception('Directive "name" not defined in modules config');
                    }
                    $className = $module['path'] . '\Module';
                    $reflection = new \ReflectionClass($className);
                    $place = dirname($reflection->getFileName());
                    $this->setModule(new $className($this, $module['name'], $module['path'], $place));
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
        $sfRouter = $this->getContainer()->get('symfony_router');
        $outputDir = $this->getContainer()->getParameter('assetic.output');
        foreach ($this->getModules() as $module) {
            call_user_func(array($module, 'registerRoutes'), $sfRouter);
            call_user_func(array($module, 'registerStatic'), $outputDir . '/static');
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
     * @return Container
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
        if (null === self::$instance) {
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
     * @return mixed
     */
    public function getConfig($param)
    {
        $this->getLogger()->alert('Access to Kernel::getConfig() method');
        return $this->getContainer()->getParameter($param);
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->getContainer()->get('router');
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->getContainer()->has('logger') ? $this->getContainer()->get('logger') : null;
    }

    /**
     * Вернет модель
     * @param string $model
     * @return Model
     * @deprecated Deprecated since 0.7, will remove since 0.8
     */
    public function getModel($model)
    {
        trigger_error('Deprecated since 0.7, will delete since 0.8');
        return $this->getContainer()->get('data.manager')->getModel($model);
    }

    /**
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->getContainer()->get('resolver');
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

    /**
     * Flushing debug info
     */
    protected function flushDebug()
    {
        $logger = $this->getLogger();
        if (!$logger) {
            return;
        }
        $exec_time = microtime(true) - AbstractKernel::$start_time;
        if (AbstractKernel::isDebug()) {
            $logger->log("Init time: " . round(AbstractKernel::$init_time, 3) . " sec.");
            $logger->log("Controller time: " . round(AbstractKernel::$controller_time, 3) . " sec.");
            $logger->log(
                "Postprocessing time: "
                . round($exec_time - AbstractKernel::$init_time - AbstractKernel::$controller_time, 3) . " sec."
            );
        }
        $logger->log("Execution time: " . round($exec_time, 3) . " sec.", 'app');
        $logger->log("Required memory: " . round(memory_get_peak_usage(true) / 1024, 3) . " kb.");
    }
}


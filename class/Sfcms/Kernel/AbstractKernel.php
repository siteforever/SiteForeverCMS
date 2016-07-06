<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Sfcms\Kernel;

use App;
use Module\Market\Object\Order;
use Sfcms\Cache\CacheInterface;
use Sfcms\Controller\Resolver;
use Sfcms\Data\DataManager;
use Sfcms\LoggerInterface;
use Sfcms\Model;
use Sfcms\Module;
use Sfcms\DeliveryManager;
use Sfcms\Request;
use Sfcms\View\Layout;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Sfcms\Router;
use Sfcms\Auth;
use Sfcms\Tpl\Driver;
use Sfcms\Basket\Base as Basket;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sfcms\Data\Watcher;
use Sfcms\View\Xhr;
use Sfcms\Model\Exception as ModelException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Stopwatch\Stopwatch;


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
            $this->_container->compile();
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
        $modules = [];
        /** @var Module $module */
        foreach($this->getModules() as $module) {
            $name = $module->getName();
            if (isset($modules[$name])) {
                throw new \LogicException(sprintf('Trying to register two modules with the same name "%s"', $name));
            }
            $modules[$module->getName()] = get_class($module);
        }

        $container = new ContainerBuilder(new ParameterBag(array(
            'root' => ROOT,
            'sfcms.root' => ROOT,
            'sf_path' => SF_PATH,
            'sfcms.path' => SF_PATH,
            'debug' => $this->isDebug(),
            'env' => $this->getEnvironment(),
            'sfcms.env' => $this->getEnvironment(),
            'sfcms.cache_dir' => $this->getCachePath(),
            'sfcms.log_dir' => $this->getLogsPath(),
            'sfcms.charset' => 'utf8',
            'modules' => $modules,
        )));

        /** @var Module $module */
        foreach($this->getModules() as $module) {
            $module->loadExtensions($container);
            $module->build($container);
            if ($this->isDebug()) {
                $container->addObjectResource($module);
            }
        }

        $extensions = array_map(function(ExtensionInterface $ext){
                return $ext->getAlias();
            }, $container->getExtensions());

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));

        $locator = new FileLocator(array($container->getParameter('root'), $container->getParameter('sfcms.path')));
        $loader = new YamlFileLoader($container, $locator);
        $loader->load(sprintf('app/config_%s.yml', $this->getEnvironment()));
        $container->set('app', $this);

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
        $outputDir = ROOT;
        foreach ($this->getModules() as $module) {
            call_user_func(array($module, 'registerRoutes'), $sfRouter);
            call_user_func(array($module, 'registerStatic'), $outputDir . '/static');
        }
    }

    /**
     * @return string
     */
    public function getContainerCacheFile()
    {
        return $this->getCachePath() . sprintf('/container_%s.php', $this->getEnvironment());
    }

    /**
     * @return string
     */
    public function getLogsPath()
    {
        return ROOT . '/runtime/logs';
    }

    /**
     * @return string
     */
    public function getCachePath()
    {
        return ROOT . '/runtime/cache/' . $this->getEnvironment();
    }

    public function redirectListener(KernelEvent $event)
    {
        if ($event->getResponse() instanceof RedirectResponse) {
            $event->stopPropagation();
        }
    }

    /**
     * Handle request
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function handleRequest(Request $request = null)
    {
        $this->getLogger()->log(str_repeat('-', 80));
        $this->getLogger()->log(sprintf('---%\'--74s---', $request->getRequestUri()));
        $this->getLogger()->log(str_repeat('-', 80));

        $this->getContainer()->set('request', $request);
        $this->getAuth()->setRequest($request);
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        $format = null;
        if ($acceptableContentTypes) {
            $format = $request->getFormat($acceptableContentTypes[0]);
        }
        $request->setRequestFormat($format);
        $request->setDefaultLocale($this->getContainer()->getParameter('locale'));

        static::$init_time = microtime(1) - static::$start_time;
        static::$controller_time = microtime(1);

        $result = null;
        /** @var Response $response */
        $response = null;
        try {
            $container = $this->getContainer();
            $tpl = $this->getTpl();
            $tpl->assign([
                    'sitename' => $container->getParameter('sitename'),
                    'debug' => $container->getParameter('kernel.debug'),
                ]);
            $this->getRouter()->setRequest($request)->routing();
            $result = $this->getResolver()->dispatch($request);
        } catch (HttpException $e) {
            $this->getLogger()->error($e->getMessage());
            switch ($request->getContentType()) {
                case 'json':
                    $response = new JsonResponse(array('error'=>1, 'msg'=>$e->getMessage()), $e->getStatusCode() ?: 500);
                    break;
                default:
                    $response = new Response($e->getMessage(), $e->getStatusCode() ?: 500);
            }
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage() . ' IN FILE ' . $e->getFile() . ':' . $e->getLine(), $e->getTrace());
            if ($this->isDebug()) {
                throw $e;
            } else {
                switch ($request->getContentType()) {
                    case 'json':
                        return new JsonResponse(array('error'=>1, 'msg'=>'Site error'), 500);
                }
                return new Response('Site error', 500);
            }
        }

        if (!$response && is_string($result)) {
            $response = new Response($result);
        } elseif ($result instanceof Response) {
            $response = $result;
        } elseif (!$response) {
            $response = new Response();
        }

        static::$controller_time = microtime(1) - static::$controller_time;

        $event = new KernelEvent($response, $request, $result);
        $this->getEventDispatcher()->dispatch(KernelEvent::KERNEL_RESPONSE, $event);

        // Выполнение операций по обработке объектов
        try {
            Watcher::instance()->performOperations();
        } catch (ModelException $e) {
            $this->getLogger()->error($e->getMessage(), $e->getTrace());
            $response->setStatusCode(500);
            $response->setContent($e->getMessage());
        } catch (PDOException $e) {
            $this->getLogger()->error($e->getMessage(), $e->getTrace());
            $response->setStatusCode(500);
            $response->setContent($e->getMessage());
        }

        return $event->getResponse();
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
     * @return DataManager
     */
    public function getDataManager()
    {
        return $this->getContainer()->get('data.manager');
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
        //trigger_error('Deprecated since 0.7, will delete since 0.8', E_USER_DEPRECATED);
        return $this->getDataManager()->getModel($model);
    }

    /**
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->getContainer()->get('sfcms.resolver');
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


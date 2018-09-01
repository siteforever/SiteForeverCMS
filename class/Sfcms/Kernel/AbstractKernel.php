<?php
/**
 * Интерфейс приложения
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Sfcms\Kernel;

use function define;
use function dump;
use Module\Market\Object\Order;
use Module\System\Controller\ErrorController;
use PDOException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function realpath;
use const ROOT;
use const SF_PATH;
use Sfcms\Auth;
use Sfcms\Basket\Base as Basket;
use Sfcms\Controller\Resolver;
use Sfcms\Data\DataManager;
use Sfcms\Data\Watcher;
use Sfcms\DeliveryManager;
use Sfcms\Form\Exception\ValidationException;
use Sfcms\Model;
use Sfcms\Model\Exception as ModelException;
use Sfcms\Module;
use Sfcms\Request;
use Sfcms\Router;
use Sfcms\Tpl\Driver;
use Sfcms\View\Layout;
use Sfcms\View\Xhr;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Stopwatch\Stopwatch;
use function var_dump;

define('SF_PATH', realpath(__DIR__ . '/..'));

// user groups
define('USER_ANONIMUS', null); // аноним
define('USER_GUEST', '0'); // гость
define('USER_USER',  '1'); // юзер
define('USER_WHOLE', '2'); // оптовый покупатель
define('USER_ADMIN', '10'); // админ

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

    /**
     * Run under test environment
     * @static
     * @return bool
     */
    static public function isTest()
    {
        return defined('TEST') && TEST;
    }

    /**
     * Run application
     * @param Request $request
     * @throws \Exception
     */
    public function run(Request $request = null)
    {
        static::$start_time = microtime(true);

        if (null === $request) {
            Request::enableHttpMethodParameterOverride();
            $request  = Request::createFromGlobals();
        }

        date_default_timezone_set($this->getContainer()->hasParameter('timezone')
            ? $this->getContainer()->getParameter('timezone') : 'Europe/Moscow');

        $response = $this->handleRequest($request);

        $this->flushDebug();
        $response->prepare($request);
        $response->send();
        $this->getEventDispatcher()->dispatch(KernelEvent::KERNEL_TERMINATE, new KernelEvent($response, $request));
    }

    /**
     * Handle request
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function handleRequest(Request $request = null)
    {
        $this->getLogger()->debug(str_repeat('-', 80));
        $this->getLogger()->debug(sprintf('---%\'--74s---', $request->getRequestUri()));
        $this->getLogger()->debug(str_repeat('-', 80));

        $this->getContainer()->set('request', $request);
        $this->getAuth()->setRequest($request);
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        $format = null;
        if ($acceptableContentTypes) {
            $format = $request->getFormat($acceptableContentTypes[0]);
        }
        $request->setRequestFormat($format);
        $request->setDefaultLocale($this->getContainer()->getParameter('language'));

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
                'debug' => $container->getParameter('debug'),
            ]);
//            $tpl->assign($this->getContainer()->getParameterBag()->all());
            try {
                $this->getRouter()->setRequest($request)->routing();
                $result = $this->getResolver()->dispatch($request);
            } catch (Router\RouterException $e) {
                throw new NotFoundHttpException($e->getMessage());
            }
        } catch (HttpException $e) {
            $controller = new ErrorController($request);
            $controller->setContainer($this->getContainer());
            $this->getTpl()->assign('this', $controller);
            $this->getLogger()->error($e->getMessage());
            $errors = [];
            if ($e instanceof ValidationException) {
                $errors = $e->getErrors();
            }
            if ('json' == $request->getContentType() ||
                'json' == $request->getFormat($request->headers->get('ACCEPT'))
            ) {
                $response = new JsonResponse(
                    ['error' => 1, 'msg' => $e->getMessage(), 'errors' => $errors],
                    $e->getStatusCode() ?: JsonResponse::HTTP_BAD_REQUEST
                );
            } else {
                $response = new Response(
                    $e->getMessage(),
                    $e->getStatusCode() ?: Response::HTTP_BAD_REQUEST
                );
            }
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage() . ' IN FILE ' . $e->getFile() . ':' . $e->getLine(), $e->getTrace());
            if ($this->isDebug()) {
                throw $e;
            } else {
                switch ($request->getContentType()) {
                    case 'json':
                        return new JsonResponse(array('error'=>1, 'msg'=>'Site error'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                }
                return new Response('Site error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent($e->getMessage());
        } catch (PDOException $e) {
            $this->getLogger()->error($e->getMessage(), $e->getTrace());
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent($e->getMessage());
        }

        return $event->getResponse();
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
        return ROOT . '/var/logs';
    }

    /**
     * @return string
     */
    public function getCachePath()
    {
        return ROOT . '/var/cache/' . $this->getEnvironment();
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return sprintf('app/config_%s.yml', $this->getEnvironment());
    }

    /**
     * @return string
     */
    public function getRoutesFile()
    {
        return ROOT . '/app/routes.yml';
    }

    /**
     * @return array
     */
    public function getModulesConfigPaths()
    {
        return [ROOT, SF_PATH];
    }

    /**
     * @return mixed
     */
    protected function getModulesConfig()
    {
        $locator = new FileLocator($this->getModulesConfigPaths());
        return require $locator->locate('app/modules.php');
    }

    /**
     * AbstractKernel constructor.
     * @param string $env
     * @param bool $debug
     * @throws \Exception
     */
    public function __construct($env, $debug = false)
    {
        $this->environment = $env;
        self::$_debug = $debug;
        if ($this->isDebug()) {
            Debug::enable(E_ALL, true);
        }

        if (null === static::$instance) {
            static::$instance = $this;
        } else {
            throw new Exception('You can not create more than one instance of Application');
        }

        if (!is_dir($this->getLogsPath())) {
            @mkdir($this->getLogsPath(), 0775, true);
        }
        if (!is_dir($this->getCachePath())) {
            @mkdir($this->getCachePath(), 0775, true);
        }

        $this->loadModules($this->getModulesConfig());

        $containerConfigCache = new ConfigCache($this->getContainerCacheFile(), $this->isDebug());

        if (!$containerConfigCache->isFresh()) {
            $container = $this->createNewContainer();
            $container->compile();
            $dumper = new PhpDumper($container);
            $containerConfigCache->write($dumper->dump());
        }
        require_once $this->getContainerCacheFile();
        $this->_container = new \ProjectServiceContainer();

        $this->getContainer()->set('app', $this);

        $this->initModules();
    }

    /**
     * @return ContainerBuilder
     * @throws \Exception
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
            'sfcms.routes.file' => $this->getRoutesFile(),
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

        $locator = new FileLocator([$container->getParameter('root'), $container->getParameter('sfcms.path')]);
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($this->getConfigPath());
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
        }
        return $this->_modules;
    }

    /**
     * Загрузка параметров модулей
     * @return array
     * @throws \Exception
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
        @trigger_error('Use container for dependencies ejection', E_USER_DEPRECATED);

        if (null === self::$instance) {
            throw new Exception('Application NOT instanced');
        }
        return self::$instance;
    }

    /**
     * Получить объект авторизации
     * @throws \Exception
     * @return Auth
     */
    public function getAuth()
    {
        return $this->getContainer()->get('auth');
    }

    /**
     * Вернет доставку
     * @param Request $request
     * @param Order $order
     * @return DeliveryManager
     * @throws \Exception
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
     * @throws \Exception
     */
    public function getTpl()
    {
        return $this->getContainer()->get('tpl');
    }

    /**
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function getConfig($param)
    {
        @trigger_error('Access to Kernel::getConfig() method', E_USER_DEPRECATED);
        $this->getLogger()->alert(sprintf('Access to %s(%s) method', __METHOD__, $param));
        return $this->getContainer()->hasParameter($param)
            ? $this->getContainer()->getParameter($param)
            : null;
    }

    /**
     * @return Router
     * @throws \Exception
     */
    public function getRouter()
    {
        return $this->getContainer()->get('router');
    }

    /**
     * @return DataManager
     * @throws \Exception
     */
    public function getDataManager()
    {
        return $this->getContainer()->get('data.manager');
    }

    /**
     * @return LoggerInterface
     * @throws \Exception
     */
    public function getLogger()
    {
        return $this->getContainer()->has('logger')
            ? $this->getContainer()->get('logger')
            : new NullLogger();
    }

    /**
     * Вернет модель
     * @param string $model
     * @return Model
     * @deprecated Deprecated since 0.7, will remove since 0.8
     * @throws \Exception
     */
    public function getModel($model)
    {
        //trigger_error('Deprecated since 0.7, will delete since 0.8', E_USER_DEPRECATED);
        return $this->getDataManager()->getModel($model);
    }

    /**
     * @return Resolver
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
     */
    protected function flushDebug()
    {
        $logger = $this->getLogger();
        if (!$logger) {
            return;
        }
        $exec_time = microtime(true) - AbstractKernel::$start_time;
        if (AbstractKernel::isDebug()) {
            $logger->debug("Init time: " . round(AbstractKernel::$init_time, 3) . " sec.");
            $logger->debug("Controller time: " . round(AbstractKernel::$controller_time, 3) . " sec.");
            $logger->debug(
                "Postprocessing time: "
                . round($exec_time - AbstractKernel::$init_time - AbstractKernel::$controller_time, 3) . " sec."
            );
        }
        $logger->debug("Execution time: " . round($exec_time, 3) . " sec.");
        $logger->debug("Required memory: " . round(memory_get_peak_usage(true) / 1024, 3) . " kb.");
    }

    /**
     * @param KernelEvent $event
     */
    public function redirectListener(KernelEvent $event)
    {
        if ($event->getResponse() instanceof RedirectResponse) {
            $event->stopPropagation();
        }
    }

    /**
     * Если контроллер вернул массив, то преобразует его в строку и сохранит в Response
     * @param KernelEvent $event
     * @return string
     * @throws \Exception
     */
    public function prepareResult(KernelEvent $event)
    {
        $response = $event->getResponse();
        $result = $event->getResult();
        $request = $event->getRequest();
        $format = $request->getRequestFormat();
        // Имеет больший приоритет, чем данные в Request-Request->content
        if (is_array($result) && ('html' == $format || null === $format)) {
            // Если надо отпарсить шаблон с данными из массива
            $this->getTpl()->assign($result);
            $template = $request->getController() . '.' . $request->getAction();
            $this->getTpl()->assign('request', $request);
            $this->getTpl()->assign('response', $response);
            $this->getTpl()->assign('auth', $this->getAuth());
            $result   = $this->getTpl()->fetch(strtolower($template));
            $response->setContent($result);
        } elseif (is_array($result) && 'json' == $format) {
            // Если надо вернуть JSON из массива
            $event->setResponse(new JsonResponse($result, $response->getStatusCode(), $response->headers->all()));
        }

        return $event;
    }

    /**
     * Перезагрузка страницы
     * @param KernelEvent $event
     *
     * @return KernelEvent
     */
    public function prepareReload(KernelEvent $event)
    {
        if ($reload = $event->getRequest()->get('reload')) {
            $event->getResponse()->setContent($event->getResponse()->getContent() . $reload);
        }
        return $event;
    }

    /**
     * Вызвать обертку для представления
     * @param KernelEvent $event
     *
     * @return KernelEvent
     * @throws \Exception
     */
    public function invokeLayout(KernelEvent $event)
    {
        $watch = (new Stopwatch())->start(__FUNCTION__);
        if ($event->getResponse() instanceof JsonResponse || $event->getRequest()->getAjax()) {
            $Layout = new Xhr($this, $this->getContainer()->getParameter('template'));
        } else {
            $Layout = new Layout($this, $this->getContainer()->getParameter('template'));
        }
        $Layout->view($event);

        $this->getLogger()->info(sprintf('Invoke layout: %.3f sec', $watch->stop(__FUNCTION__)->getDuration() / 1000));
        return $event;
    }

    /**
     * @param KernelEvent $event
     */
    public function createSignature(KernelEvent $event)
    {
        if (!$this->getContainer()->hasParameter('silent')) {
            $event->getResponse()->headers->set('X-Powered-By', 'SiteForeverCMS');
        }
    }
}


<?php
// user groups
define('USER_ANONIMUS', null); // аноним
define('USER_GUEST', '0'); // гость
define('USER_USER',  '1'); // юзер
define('USER_WHOLE', '2'); // оптовый покупатель
define('USER_ADMIN', '10'); // админ

if (!defined('SF_PATH')) {
    define('SF_PATH', realpath(__DIR__ . '/..'));
}

use Module\Market\Object\Order;
use Psr\Log\LoggerInterface;
use Sfcms\Auth;
use Sfcms\Data\DataManager;
use Sfcms\DeliveryManager;
use Sfcms\Model;
use Sfcms\Module;
use Sfcms\Request;
use Sfcms\Tpl\Driver;
use Symfony\Component\Config\Loader\LoaderInterface;
use Sfcms\View\Layout;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Класс приложение
 * FrontController
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */
class App extends Kernel
{
    /** @var Module[] */
    protected $modules;

    /** @var array */
    protected $controllers;

    /** @var ContainerInterface */
    static protected $containerLocator;

    /**
     * Constructor.
     *
     * @param string  $environment The environment
     * @param bool    $debug       Whether to enable debugging or not
     *
     * @api
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
    }

    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances.
     *
     * @api
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            //new NoiseLabs\Bundle\SmartyBundle\SmartyBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Siteforever\Bundle\CmsBundle\SiteforeverCmsBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * Returns an array of modules to register.
     *
     * @return Module[] An array of bundle instances.
     *
     * @api
     */
    public function registerModules()
    {
        $locator = new FileLocator(array(ROOT, SF_PATH));
        $modules = require $locator->locate('app/modules.php');
        return $modules;
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @api
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config_%s.yml', $this->getRootDir(), $this->getEnvironment()));
    }

    /**
     * Run application
     * @param $request
     */
    public function run(Request $request = null)
    {
        if (null === $request) {
            Request::enableHttpMethodParameterOverride();
            $request  = Request::createFromGlobals();
        }

        if ($this->isDebug()) {
            Debug::enable();
        }

        $response = $this->handle($request);
        $response->prepare($request);
        $response->send();
        $this->terminate($request, $response);
    }

    /**
     * Boots the current kernel.
     *
     * @api
     */
    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        if ($this->loadClassCache) {
            $this->doLoadClassCache($this->loadClassCache[0], $this->loadClassCache[1]);
        }

        // init bundles
        $this->initializeBundles();

        // init modules
        $this->initializeModules();

        // init container
        $this->initializeContainer();

        foreach ($this->getBundles() as $bundle) {
            $bundle->setContainer($this->container);
            $bundle->boot();
        }

        static::$containerLocator = $this->container;

        $this->booted = true;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    protected function initializeModules()
    {
        foreach ($this->registerModules() as $module) {
            if (!isset($module['path'])) {
                throw new InvalidArgumentException('Directive "path" not defined in modules config');
            }
            if (!isset($module['name'])) {
                throw new InvalidArgumentException('Directive "name" not defined in modules config');
            }
            $className = $module['path'] . '\Module';
            $reflection = new \ReflectionClass($className);
            $place = dirname($reflection->getFileName());
            /** @var Module $moduleObj */
            $moduleObj = new $className($this, $module['name'], $module['path'], $place);
            if (isset($this->modules[$module['name']])) {
                throw new LogicException(sprintf('Trying to register two modules with the same name "%s"', $module['name']));
            }
            $this->modules[$module['name']] = $moduleObj;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    protected function prepareContainer(ContainerBuilder $container)
    {
        foreach ($this->modules as $moduleName => $moduleObj) {
            $moduleObj->loadExtensions($container);
            $moduleObj->build($container);
            if ($this->debug) {
                $container->addObjectResource($moduleObj);
            }
        }
        $extensions = array_values(array_map(function(ExtensionInterface $ext){
                return $ext->getAlias();
            }, $container->getExtensions()));

        foreach ($this->bundles as $bundle) {
            if ($extension = $bundle->getContainerExtension()) {
                $container->registerExtension($extension);
                $extensions[] = $extension->getAlias();
            }

            if ($this->debug) {
                $container->addObjectResource($bundle);
            }
        }
        foreach ($this->bundles as $bundle) {
            $bundle->build($container);
        }

        $container->setAlias('app', 'kernel');

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();

        $kernelModules = array();
        foreach ($this->modules as $module) {
            $kernelModules[$module->getName()] = get_class($module);
        }
        $parameters['kernel.modules'] = $kernelModules;
        $parameters['kernel.sfcms_dir'] = SF_PATH;
        $parameters['sf_path'] = SF_PATH;

        return $parameters;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $locator = new FileLocator(array(ROOT, __DIR__));
            $this->rootDir = $locator->locate(sprintf('app'));
        }

        return $this->rootDir;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getCacheDir()
    {
        return realpath($this->getRootDir().'/..') . '/var/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getLogDir()
    {
        return realpath($this->getRootDir().'/..') . '/var/logs';
    }

    /**
     * Загружает список известных системе контроллеров
     * <p>Загружется список установленных модулей.</p>
     * <p>Из них формируется список контроллеров, которые имеются в системе</p>

     * @return array
     */
    public function getControllers()
    {
        if (null === $this->controllers) {
            $this->controllers = array();
            foreach ($this->getModules() as $name => $module) {
                $config = $module->config();
                if (isset($config['controllers'])) {
                    foreach ($config['controllers'] as $controller => $params) {
                        $params['module'] = $name;
                        $this->controllers[strtolower($controller)] = $params;
                    }
                }
            }
        }

        return $this->controllers;
    }


    /**
     * @return \Sfcms\Module[]
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @param $name
     * @return Module
     */
    public function getModule($name)
    {
        return $this->modules[$name];
    }

    /**
     * @static
     * @throws Exception
     * @deprecated since version 0.7, will be removed from 0.8
     * @return App
     */
    static public function cms()
    {
        return static::$containerLocator->get('kernel');
    }

    public function get($service)
    {
      return $this->getContainer()->get($service);
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
     * Вернет объект кэша
     *
     * @return CacheInterface
     * @throws Exception
     */
    public function getCacheManager()
    {
        $this->getContainer()->get('cache');
    }

    public function redirectListener(KernelEvent $event)
    {
        if ($event->getResponse() instanceof RedirectResponse) {
            $event->stopPropagation();
        }
    }

    /**
     * Вернет доставку
     * @param Request $request
     * @param Order $order
     * @return DeliveryManager
     */
    public function getDeliveryManager(Request $request, Order $order)
    {
        return new DeliveryManager($request, $order, $this->getEventDispatcher());
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
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->getContainer()->get('event_dispatcher');
    }
    
    public function createSignature(Sfcms\Kernel\KernelEvent $event)
    {
        if (!$this->getContainer()->hasParameter('silent')) {
            $event->getResponse()->headers->set('X-Powered-By', 'SiteForeverCMS');
        }
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

}

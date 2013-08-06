<?php
/**
 * Модуль
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

use App;
use Sfcms\Kernel\AbstractKernel;
use Sfcms\Tpl\Directory;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\Router;

abstract class Module extends Component
{
    /** @var App */
    protected $app;

    /** @var string */
    protected $name;

    /** @var string */
    protected $ns;

    /** @var string */
    protected $path;

    /** @param array */
    protected static $controllers = null;


    public function __construct(App $app, $name, $ns, $path)
    {
        $this->app = $app;
        $this->name = $name;
        $this->ns = $ns;
        $this->path = $path;
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public abstract function config();

    /**
     * Вернет поле, которое связывает страницу с ее модулем
     * @static
     * @return string
     */
    public static function relatedField()
    {
        return 'link';
    }

    public function registerService(ContainerBuilder $container)
    {
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNs()
    {
        return $this->ns;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Класс, который обозначает конкретный модуль
     * @static
     * @param $controller
     * @return string
     */
    public static function getModuleClass( $controller )
    {
        if ( null === self::$controllers ) {
            self::$controllers = App::cms()->getControllers();
        }
        if ( isset( self::$controllers[ $controller ] ) ) {
            $config = self::$controllers[ $controller ];
            return    '\\Module\\'
                    . ( isset( $config['module'] ) ? $config['module'] : ucfirst(strtolower($controller)) )
                    . '\\Module';
        }
        throw new \RuntimeException(sprintf('Contoroller %s not defined', $controller),404);
    }

    /**
     * Название связывающей модели
     * @static
     * @return string
     */
    public static function relatedModel()
    {
//        return 'Page';
        return null;
    }

    public function admin_menu()
    {
        return array(
//            array(
//                'name' => 'module_name',
//                'url'  => 'module/path',
//            ),
        );
    }

    /**
     * Register directories View and Widget in current module, if exist
     * @param Directory $tpl
     */
    public function registerViewsPath(Directory $tpl)
    {
        if (is_dir($this->getPath().'/View')) {
            $tpl->addTplDir($this->getPath().'/View');
        }
        if (is_dir($this->getPath().'/Widget')) {
            $tpl->addWidgetsDir($this->getPath().'/Widget');
        }
    }

    /**
     * Registering custom routes of module in router component
     * @param Router $router
     */
    public function registerRoutes(Router $router)
    {
    }

    /**
     * Finds and registers Commands.
     *
     * Override this method if your bundle commands do not follow the conventions:
     *
     * * Commands are in the 'Command' sub-directory
     * * Commands extend Symfony\Component\Console\Command\Command
     *
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application)
    {
        if (!is_dir($dir = $this->getPath().'/Command')) {
            return;
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($dir);

        $prefix = $this->getNs().'\\Command';
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $ns = $prefix;
            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\'.strtr($relativePath, '/', '\\');
            }
            $r = new \ReflectionClass($ns.'\\'.$file->getBasename('.php'));
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract()) {
                $application->add($r->newInstance());
            }
        }
    }
}

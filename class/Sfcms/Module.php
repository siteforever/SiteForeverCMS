<?php
/**
 * Модуль
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

use App;
use Sfcms\Kernel\KernelBase;
use Sfcms\Tpl\Driver;
use Sfcms_Http_Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
            self::$controllers = App::getInstance()->getControllers();
        }
        if ( isset( self::$controllers[ $controller ] ) ) {
            $config = self::$controllers[ $controller ];
            return    '\\Module\\'
                    . ( isset( $config['module'] ) ? $config['module'] : ucfirst(strtolower($controller)) )
                    . '\\Module';
        }
        print_r( self::$controllers );
//        die(sprintf('Contoroller %s not defined', $controller));
//        throw new Sfcms_Http_Exception(sprintf('Contoroller %s not defined', $controller),404);
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

    public function registerViewsPath(Driver $tpl)
    {
        if (is_dir($this->getPath().'/View')) {
            $tpl->addTplDir($this->getPath().'/View');
        }
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

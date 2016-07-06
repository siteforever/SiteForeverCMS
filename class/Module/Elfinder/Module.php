<?php
/**
 * Module Elfinder
 * @generator SiteForeverGenerator
 */

namespace Module\Elfinder;

use Module\Elfinder\DependencyInjection\Compiler\ElfinderPass;
use Module\Elfinder\DependencyInjection\ElfinderExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Module extends SfModule
{
    public function init()
    {
    }

    /**
     * Return array config of module
     * @return array
     */
    public static function config()
    {
        return array(
            'controllers' => array(
                'elfinder' => array(),
            ),
//            'models' => array(
//                'SomeName' => 'Module\Elfinder\Model\SomeModel',
//            ),
        );
    }

    public function build(ContainerBuilder $container)
    {
        $container->registerExtension(new ElfinderExtension());
        $container->addCompilerPass(new ElfinderPass());
    }

    public function registerRoutes()
    {
        $routes = new RouteCollection();
        $routes->add('elfinder',
            new Route('/elfinder',
                array('_controller'=>'elfinder', '_action'=>'finder')
            ));
        $routes->add('elfinder/finder',
            new Route('/elfinder/finder',
                array('_controller'=>'elfinder', '_action'=>'finder')
            ));
        $routes->add('elfinder/connector',
            new Route('/elfinder/connector',
                array('_controller'=>'elfinder', '_action'=>'connector')
            ));

        return $routes;
    }


    public function admin_menu()
    {
        return array(
            array(
                'name'=> 'service',
                'sub' => array(
                    array(
                        'name'  => 'filemanager',
                        'url'   => 'elfinder/finder',
                        'class' => 'filemanager',
                    ),
                ),
            ),
        );
    }
}

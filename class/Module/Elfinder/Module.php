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
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    public function init()
    {
    }

    /**
     * Return array config of module
     * @return array
     */
    public function config()
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

    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new ElfinderExtension());
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ElfinderPass());
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
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

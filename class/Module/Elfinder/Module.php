<?php
/**
 * Module Elfinder
 * @generator SiteForeverGenerator
 */

namespace Module\Elfinder;

use Sfcms\Module as SfModule;
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

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('elfinder',
            new Route('/elfinder',
                array('_controller'=>'elfinder', '_action'=>'elfinder')
            ));
        $routes->add('elfinder/elfinder',
            new Route('/elfinder/elfinder',
                array('_controller'=>'elfinder', '_action'=>'elfinder')
            ));
        $routes->add('elfinder/connector',
            new Route('/elfinder/connector',
                array('_controller'=>'elfinder', '_action'=>'connector')
            ));
    }


    public function admin_menu()
    {
        return array(
//            array(
//                'name'  => 'Elfinder',
//                'url'   => 'admin/elfinder',
//            )
        );
    }
}

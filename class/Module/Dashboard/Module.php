<?php
/**
 * Module Dashboard
 * @generator SiteForeverGenerator
 */

namespace Module\Dashboard;

use Sfcms\Module as SfModule;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
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
    public static function config()
    {
        return array(
            'controllers' => array(
                'dashboard' => array(),
            ),
//            'models' => array(
//                'SomeName' => 'Module\Dashboard\Model\SomeModel',
//            ),
        );
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'dashboard',
                'glyph' => 'dashboard',
                'url'   => 'admin',
            )
        );
    }

    public function registerRoutes()
    {
        $routes = new RouteCollection();
        $routes->add('admin',
            new Route('/admin',
                array('_controller'=>'dashboard', '_action'=>'index')
            ));

        return $routes;
    }
}

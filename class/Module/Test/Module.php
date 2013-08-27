<?php
/**
 * Module Test
 * @generator SiteForeverGenerator
 */

namespace Module\Test;

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
                'testForm' => array( 'class' => 'Controller\Form', ),
            ),
//            'models' => array(
//                'SomeName' => 'Module\Test\Model\SomeModel',
//            ),
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('test.form/file', new Route('/test.form/file', array(
                '_controller' => 'testForm',
                '_action' => 'file',
            )));
    }


    public function admin_menu()
    {
        return array(
//            array(
//                'name'  => 'Test',
//                'url'   => 'admin/test',
//            )
        );
    }
}

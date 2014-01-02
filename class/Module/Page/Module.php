<?php
/**
 * Модуль страницы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Page;

use Module\Page\DependencyInjection\PageExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    public static function relatedField()
    {
        return 'id';
    }

    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new PageExtension());
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        // SiteMap Controller
        $routes->add('sitemap',
            new Route('/sitemap',
                array('_controller'=>'sitemap', '_action'=>'index')
            ));
        $routes->add('sitemap_xml',
            new Route('/sitemap.xml',
                array('_controller'=>'sitemap', '_action'=>'xml')
            ));

        // Page Controller
        $routes->add('page/index',
            new Route('/',
                array('_controller'=>'page', '_action'=>'index', '_id'=>1)
            ));
        $routes->add('page/admin',
            new Route('/page/admin',
                array('_controller'=>'page', '_action'=>'admin')
            ));
        $routes->add('page/create',
            new Route('/page/create',
                array('_controller'=>'page', '_action'=>'create')
            ));
        $routes->add('page/add',
            new Route('/page/add',
                array('_controller'=>'page', '_action'=>'add')
            ));
        $routes->add('page/edit',
            new Route('/page/edit',
                array('_controller'=>'page', '_action'=>'edit')
            ));
        $routes->add('page/save',
            new Route('/page/save',
                array('_controller'=>'page', '_action'=>'save')
            ));
        $routes->add('page/delete',
            new Route('/page/delete',
                array('_controller'=>'page', '_action'=>'delete')
            ));
        $routes->add('page/resort',
            new Route('/page/resort',
                array('_controller'=>'page', '_action'=>'resort')
            ));
        $routes->add('page/hidden',
            new Route('/page/hidden',
                array('_controller'=>'page', '_action'=>'hidden')
            ));
        $routes->add('page/realias',
            new Route('/page/realias',
                array('_controller'=>'page', '_action'=>'realias')
            ));
    }


    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'Page'  => array(),
                'Sitemap'  => array(),
            ),
            'models' => array(
                'Page' => 'Module\\Page\\Model\\PageModel',
            ),
        );
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'structure',
                'url'   => 'page/admin',
            )
        );
    }
}

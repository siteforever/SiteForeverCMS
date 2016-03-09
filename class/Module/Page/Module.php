<?php
/**
 * Модуль страницы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Page;

use Module\Page\DependencyInjection\PageExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\YamlFileLoader;
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
        $locator = new FileLocator(__DIR__);
        $loader = new YamlFileLoader($locator);
        $routes->addCollection($loader->load('routes.yml'));
    }


    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => [
                'Page'=>[],
            ],
            'models' => [
                'Page' => 'Module\\Page\\Model\\PageModel',
            ],
        );
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'structure',
                'url'   => 'page/admin',
                'glyph' => 'tree-deciduous',
            )
        );
    }
}

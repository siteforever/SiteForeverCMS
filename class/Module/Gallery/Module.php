<?php
/**
 * Модуль Галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Gallery;

use Module\Gallery\DependencyInjection\GalleryExtension;
use Sfcms\Model;
use Sfcms\Module as SfModule;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    public static function relatedField()
    {
        return 'id';
    }

    public static function relatedModel()
    {
        return 'GalleryCategory';
    }

    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new GalleryExtension());
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'Gallery'   => array(),
            ),
            'models' => array(
                'Gallery'         => 'Module\Gallery\Model\GalleryModel',
                'GalleryCategory' => 'Module\Gallery\Model\CategoryModel',
            ),
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $locator = new FileLocator(__DIR__);
        $loader = new YamlFileLoader($locator);
        $routes->addCollection($loader->load('routes.yml'));
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Галерея',
                'url'   => 'gallery/admin',
                'glyph' => 'picture',
            )
        );
    }
}

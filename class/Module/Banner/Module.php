<?php
/**
 * Модуль баннеров
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Banner;

use Sfcms\Module as SfModule;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return include_once __DIR__ . '/config.php';
    }

    public static function relatedModel()
    {
        return 'Page';
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('banner/admin',
            new Route('/banner/admin',
                array('_controller'=>'banner', '_action'=>'admin')
            ));
        $routes->add('banner/redirectBanner',
            new Route('/banner/redirectbanner/id/{id}',
                array('_controller'=>'banner', '_action'=>'redirectBanner')
            ));
        $routes->add('banner/cat',
            new Route('/banner/cat/id/{id}',
                array('_controller'=>'banner', '_action'=>'cat')
            ));
        $routes->add('banner/saveCat',
            new Route('/banner/savecat',
                array('_controller'=>'banner', '_action'=>'saveCat')
            ));
        $routes->add('banner/saveCat/id',
            new Route('/banner/savecat/id/{id}',
                array('_controller'=>'banner', '_action'=>'saveCat')
            ));
        $routes->add('banner/delCat',
            new Route('/banner/delcat/id/{id}',
                array('_controller'=>'banner', '_action'=>'delCat')
            ));
        $routes->add('banner/del',
            new Route('/banner/del/id/{id}',
                array('_controller'=>'banner', '_action'=>'del')
            ));
        $routes->add('banner/edit',
            new Route('/banner/edit/id/{id}',
                array('_controller'=>'banner', '_action'=>'edit'),
                array('id'=>'\d+')
            ));
        $routes->add('banner/edit/cat',
            new Route('/banner/edit/cat/{cat}',
                array('_controller'=>'banner', '_action'=>'edit'),
                array('cat'=>'\d+')
            ));
        $routes->add('banner/save',
            new Route('/banner/save',
                array('_controller'=>'banner', '_action'=>'save')
            ));
    }


    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Баннеры',
                'url'   => 'banner/admin',
            )
        );
    }
}

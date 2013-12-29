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
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

    public function init()
    {
        // todo Реализовать через Extension
//        $model = Model::getModel('GalleryCategory');
//        $dispatcher = $this->app->getEventDispatcher();
//        $dispatcher->addListener('plugin.page-gallery.save.start', array($model,'pluginPageSaveStart'));
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
        $routes->add('gallery',
            new Route('/gallery',
                array('_controller'=>'gallery', '_action'=>'index')
            ));
        $routes->add('gallery/admin',
            new Route('/gallery/admin',
                array('_controller'=>'gallery', '_action'=>'admin')
            ));
        $routes->add('gallery/switchimg',
            new Route('/gallery/switchimg',
                array('_controller'=>'gallery', '_action'=>'switchimg')
            ));
        $routes->add('gallery/delete',
            new Route('/gallery/delete',
                array('_controller'=>'gallery', '_action'=>'delete')
            ));
        $routes->add('gallery/editcat',
            new Route('/gallery/editcat',
                array('_controller'=>'gallery', '_action'=>'editcat')
            ));
        $routes->add('gallery/delcat',
            new Route('/gallery/delcat',
                array('_controller'=>'gallery', '_action'=>'delcat')
            ));
        $routes->add('gallery/list',
            new Route('/gallery/list',
                array('_controller'=>'gallery', '_action'=>'list')
            ));
        $routes->add('gallery/edit',
            new Route('/gallery/edit',
                array('_controller'=>'gallery', '_action'=>'edit')
            ));
        $routes->add('gallery/realias',
            new Route('/gallery/realias',
                array('_controller'=>'gallery', '_action'=>'realias')
            ));
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Галерея',
                'url'   => 'gallery/admin',
            )
        );
    }
}

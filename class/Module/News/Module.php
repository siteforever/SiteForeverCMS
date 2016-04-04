<?php
/**
 * Модуль новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\News;

use Module\News\DependencyInjection\NewsExtension;
use Sfcms\Model;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    public static function relatedModel()
    {
        return 'NewsCategory';
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'News'  => array(),
                'NewsCategory'  => array(),
                'Rss'   => array(),
            ),
            'models' => array(
                'News'         => 'Module\\News\\Model\\NewsModel',
                'NewsCategory' => 'Module\\News\\Model\\CategoryModel',
            ),
        );
    }

    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new NewsExtension());
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('rss',
            new Route('/rss',
                array('_controller'=>'rss', '_action'=>'index')
            ));

        $routes->add('news/admin',
            new Route('/news/admin',
                array('_controller'=>'news', '_action'=>'admin')
            ));
        $routes->add('news/list',
            new Route('/news/list',
                array('_controller'=>'news', '_action'=>'list')
            ));
        $routes->add('news/edit',
            new Route('/news/edit',
                array('_controller'=>'news', '_action'=>'edit')
            ));
        $routes->add('news/delete',
            new Route('/news/delete',
                array('_controller'=>'news', '_action'=>'delete')
            ));
        $routes->add('news/catedit',
            new Route('/news/catedit',
                array('_controller'=>'newscategory', '_action'=>'catEdit')
            ));
        $routes->add('news/catdelete',
            new Route('/news/catdelete',
                array('_controller'=>'newscategory', '_action'=>'catDelete')
            ));
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Новости/статьи',
                'url'   => 'news/admin',
                'glyph' => 'bullhorn',
            )
        );
    }
}

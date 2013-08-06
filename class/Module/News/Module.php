<?php
/**
 * Модуль новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\News;

use Sfcms\Model;
use Sfcms\Module as SfModule;
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
        return include_once __DIR__ . '/config.php';
    }

    public function init()
    {
        $model = Model::getModel('NewsCategory');
        $dispatcher = $this->app->getEventDispatcher();
        $dispatcher->addListener('plugin.page-news.save.start', array($model,'pluginPageSaveStart'));
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
            new Route('/news/list/id/{id}',
                array('_controller'=>'news', '_action'=>'list')
            ));
        $routes->add('news/edit/cat',
            new Route('/news/edit/cat/{cat}',
                array('_controller'=>'news', '_action'=>'edit')
            ));
        $routes->add('news/edit',
            new Route('/news/edit',
                array('_controller'=>'news', '_action'=>'edit')
            ));
        $routes->add('news/edit/id',
            new Route('/news/edit/id/{id}',
                array('_controller'=>'news', '_action'=>'edit')
            ));
        $routes->add('news/delete',
            new Route('/news/delete/id/{id}',
                array('_controller'=>'news', '_action'=>'delete')
            ));
        $routes->add('news/catedit',
            new Route('/news/catedit/id/{id}',
                array('_controller'=>'news', '_action'=>'catedit')
            ));
        $routes->add('news/catdelete',
            new Route('/news/catdelete/id/{id}',
                array('_controller'=>'news', '_action'=>'catdelete')
            ));
    }


    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Новости/статьи',
                'url'   => 'news/admin',
            )
        );
    }
}

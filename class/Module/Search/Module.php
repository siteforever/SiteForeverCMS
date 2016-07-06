<?php
/**
 * Модуль поиска
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Search;

use Sfcms\Model;
use Sfcms\Module as SfModule;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public static function config()
    {
        return array(
            'controllers' => array(
                'search' => array('Module\\Search\\Controller\\SearchController'),
            ),
            'model' => array(
//                'Search' => 'Module\\Search\\Model\\SearchModel',
            ),
        );
    }

    public function admin_menu()
    {
        return array(
//            array(
//                'name' => 'Поиск',
//                'url'  => 'search/admin',
//            ),
        );
    }




    public function registerRoutes()
    {
        $routes = new RouteCollection();
        $routes->add('search',
            new Route('/search',
                array('_controller'=>'search', '_action'=>'index')
            ));
//        $routes->add('search/admin',
//            new Route('/search/admin',
//                array('_controller'=>'search', '_action'=>'admin')
//            ));
        $routes->add('search/indexing',
            new Route('/search/indexing',
                array('_controller'=>'search', '_action'=>'indexing')
            ));

        return $routes;
    }


    public function init()
    {
    }
}

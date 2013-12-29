<?php
/**
 * Модуль гостевой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Guestbook;

use Sfcms\Module as SfModule;
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
        return 'Page';
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'Guestbook' => array(),
            ),
            'models' => array(
                'Guestbook' => 'Module\\Guestbook\\Model\\GuestbookModel',
            ),
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('guestbook',
            new Route('/guestbook',
                array('_controller'=>'guestbook', '_action'=>'index')
            ));
        $routes->add('guestbook/admin',
            new Route('/guestbook/admin',
                array('_controller'=>'guestbook', '_action'=>'admin')
            ));
        $routes->add('guestbook/edit',
            new Route('/guestbook/edit',
                array('_controller'=>'guestbook', '_action'=>'edit')
            ));

    }


    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Гостевая',
                'url'   => 'guestbook/admin',
            )
        );
    }
}

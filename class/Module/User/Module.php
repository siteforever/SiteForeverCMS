<?php
/**
 * Модуль пользователя
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\User;

use Sfcms\Model;
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
        return array(
            'controllers' => array(
                'user' => array(),
            ),
            'models'      => array(
                'User' => 'Module\\User\\Model\\UserModel',
            ),
        );
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Пользователи',
                'url'   => 'user/admin',
                'glyph' => 'user',
            ),
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('user',
            new Route('/user',
                array('_controller'=>'user', '_action'=>'index')
            ));
        $routes->add('user/cabinet',
            new Route('/user/cabinet',
                array('_controller'=>'user', '_action'=>'cabinet')
            ));
        $routes->add('user/admin',
            new Route('/user/admin',
                array('_controller'=>'user', '_action'=>'admin')
            ));
        $routes->add('user/save',
            new Route('/user/save',
                array('_controller'=>'user', '_action'=>'save')
            ));
        $routes->add('user/logout',
            new Route('/user/logout',
                array('_controller'=>'user', '_action'=>'logout')
            ));
        $routes->add('user/login',
            new Route('/user/login',
                array('_controller'=>'user', '_action'=>'login')
            ));
        $routes->add('user/edit',
            new Route('/user/edit',
                array('_controller'=>'user', '_action'=>'edit')
            ));
        $routes->add('user/register',
            new Route('/user/register',
                array('_controller'=>'user', '_action'=>'register')
            ));
        $routes->add('user/recovery',
            new Route('/user/recovery',
                array('_controller'=>'user', '_action'=>'recovery')
            ));
        $routes->add('user/restore',
            new Route('/user/restore',
                array('_controller'=>'user', '_action'=>'restore')
            ));
        $routes->add('user/password',
            new Route('/user/password',
                array('_controller'=>'user', '_action'=>'password')
            ));
    }


}

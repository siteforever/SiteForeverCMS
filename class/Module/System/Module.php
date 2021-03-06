<?php
/**
 * Модуль системы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\System;

use Module\System\DependencyInjection\CaptchaExtension;
use Module\System\DependencyInjection\Compiler\AuthPass;
use Module\System\DependencyInjection\Compiler\EventSubscriberPass;
use Module\System\DependencyInjection\Compiler\SiteAdminJsPass;
use Module\System\DependencyInjection\SystemExtension;
use Sfcms\Model;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    public function build(ContainerBuilder $container)
    {
        $container->registerExtension(new SystemExtension());
        $container->registerExtension(new CaptchaExtension());

        $container->addCompilerPass(new SiteAdminJsPass());
        $container->addCompilerPass(new EventSubscriberPass());
        $container->addCompilerPass(new AuthPass());
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public static function config()
    {
        $controllers = array(
            'captcha'   => array(),
            'generator' => array(),
            'error'     => array(),
            'log'       => array(),
            'routes'    => array(),
            'setting'   => array(),
            'static'    => array(),
            'system'    => array(),
        );
        $models = array(
            'Routes'    => 'Module\\System\\Model\\RoutesModel',
            'Settings'  => 'Module\\System\\Model\\SettingsModel',
            'Session'   => 'Module\\System\\Model\\SessionModel',
            'Templates' => 'Module\\System\\Model\\TemplatesModel',
            'Log'       => 'Module\\System\\Model\\LogModel',
        );
        return array(
            'controllers' => $controllers,
            'models'      => $models,
        );
    }

    public function registerRoutes()
    {
        $routes = new RouteCollection();

        $routes->add('captcha',
            new Route('/captcha',
                array('_controller'=>'captcha', '_action'=>'index')
            ));

        $routes->add('generator',
            new Route('/generator',
                array('_controller'=>'generator', '_action'=>'index')
            ));
        $routes->add('generator/generate',
            new Route('/generator/generate',
                array('_controller'=>'generator', '_action'=>'generate')
            ));

        $routes->add('log/admin',
            new Route('/log/admin',
                array('_controller'=>'log', '_action'=>'admin')
            ));
        $routes->add('log/grid',
            new Route('/log/grid',
                array('_controller'=>'log', '_action'=>'grid')
            ));

        $routes->add('setting/admin',
            new Route('/log/setting',
                array('_controller'=>'setting', '_action'=>'admin')
            ));
        $routes->add('setting/save',
            new Route('/setting/save',
                array('_controller'=>'setting', '_action'=>'save')
            ));

        $routes->add('static',
            new Route('/static/{alias}',
                array('_controller'=>'static', '_action'=>'asset')
            ));

        $routes->add('system',
            new Route('/system',
                array('_controller'=>'system', '_action'=>'index')
            ));
        $routes->add('system/assembly',
            new Route('/system/assembly',
                array('_controller'=>'system', '_action'=>'assembly')
            ));

        return $routes;
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Журнал',
                'url'   => 'log/admin',
            ),
//            array(
//                'name' => 'Система',
//                'sub' => array(
//                    array(
//                        'name'  => 'Маршруты',
//                        'url'   => 'routes/admin',
//                    ),
//                    array(
//                        'name'  => 'Конфигурация системы',
//                        'url'   => 'system',
//                    ),
//                    array(
//                        'name'  => 'Настройка',
//                        'url'   => 'setting/admin',
//                    ),
//                    array(
//                        'name'  => 'Генератор',
//                        'url'   => 'generator',
//                    ),
//                ),
//            ),
        );
    }
}

<?php
/**
 * Модуль системы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\System;

use Module\System\DependencyInjection\CaptchaExtension;
use Module\System\DependencyInjection\Compiler\DatabasePass;
use Module\System\DependencyInjection\Compiler\EventSubscriberPass;
use Module\System\DependencyInjection\DatabaseExtension;
use Module\System\DependencyInjection\AsseticExtension;
use Module\System\DependencyInjection\SystemExtension;
use Sfcms\Model;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new SystemExtension());
        $container->registerExtension(new DatabaseExtension());
        $container->registerExtension(new AsseticExtension());
        $container->registerExtension(new CaptchaExtension());
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new EventSubscriberPass());
        $container->addCompilerPass(new DatabasePass());
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
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
            'Comments'  => 'Module\\System\\Model\\CommentsModel',
            'Module'    => 'Module\\System\\Model\\ModuleModel',
            'Routes'    => 'Module\\System\\Model\\RoutesModel',
            'Settings'  => 'Module\\System\\Model\\SettingsModel',
            'Session'   => 'Module\\System\\Model\\SessionModel',
            'Templates' => 'Module\\System\\Model\\TemplatesModel',
            'Log'       => 'Module\\System\\Model\\LogModel',
        );
        if ($this->app->isDebug()) {
            $models['Test'] = 'Module\\System\\Model\\TestModel';
        }
        return array(
            'controllers' => $controllers,
            'models'      => $models,
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();

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

    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Пользователи',
                'url'   => 'user/admin',
            ),
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
            array(
                'name'  => 'Выход',
                'url'   => 'user/logout',
            ),
        );
    }
}

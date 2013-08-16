<?php
/**
 * Модуль системы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\System;

use Assetic\Asset\BaseAsset;
use Assetic\Asset\GlobAsset;
use Assetic\AssetManager;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Model;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
                'captcha'   => array(),
                'generator' => array(),
                'error'     => array(),
                'log'       => array(),
                'routes'    => array(),
                'setting'   => array(),
                'static'    => array(),
                'system'    => array(),
            ),
            'models'      => array(
                'Comments'  => 'Module\\System\\Model\\CommentsModel',
                'Module'    => 'Module\\System\\Model\\ModuleModel',
                'Routes'    => 'Module\\System\\Model\\RoutesModel',
                'Settings'  => 'Module\\System\\Model\\SettingsModel',
                'Session'   => 'Module\\System\\Model\\SessionModel',
                'Templates' => 'Module\\System\\Model\\TemplatesModel',
                'Log'       => 'Module\\System\\Model\\LogModel',
            ),
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('admin',
            new Route('/admin',
                array('_controller'=>'page', '_action'=>'admin')
            ));

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

    public function registerService(ContainerBuilder $container)
    {
        // Mail transport defintion
        switch ($container->getParameter('mailer_transport')) {
            case 'smtp':
                $container->register('mailer_transport', 'Swift_SmtpTransport')
                    ->addArgument('%mailer_host%')
                    ->addArgument('%mailer_port%')
                    ->addArgument('%mailer_security%')
                    ->addMethodCall('setUsername', array('%mailer_username%'))
                    ->addMethodCall('setPassword', array('%mailer_password%'))
                ;
                break;
            case 'gmail':
//                http://stackoverflow.com/a/4691183/2090796
                $container->register('mailer_transport', 'Swift_SmtpTransport')
                    ->addArgument('smtp.gmail.com')
                    ->addArgument(465)
                    ->addArgument('ssl')
                    ->addMethodCall('setUsername', array('%mailer_username%'))
                    ->addMethodCall('setPassword', array('%mailer_password%'))
                    ->addMethodCall('setAuthMode', array('login'))
                ;
                break;
            case 'null':
                $container->register('mailer_transport', 'Swift_NullTransport');
                break;
            default:
                $container->register('mailer_transport', 'Swift_SendmailTransport');
        }

        /** @var AssetManager $am */
//        $am = $container->get('assetManager');
//        $images = new GlobAsset(realpath(__DIR__.'/Static/images/*'));
//        /** @var BaseAsset $img */
//        foreach($images as $img) {
//            $am->set($img->getTargetPath(), $img);
//        }
    }


    public function init()
    {
        $model = Model::getModel('Module\\System\\Model\\LogModel');
        $dispatcher = $this->app->getEventDispatcher();
        $dispatcher->addListener('save.start', array($model,'pluginAllSaveStart'));
        $dispatcher->addListener('kernel.response', array($this, 'onKernelResponseImage'));
        $dispatcher->addListener('kernel.response', array($this, 'onKernelResponse'));
    }


    /**
     * Handling the response
     * @param KernelEvent $event
     */
    public function onKernelResponse(KernelEvent $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof JsonResponse && 403 == $response->getStatusCode()) {
            if (!$this->app->getAuth()->isLogged()) {
                $response = new RedirectResponse($this->app->getRouter()->createLink('user/login'));
                $event->setResponse($response);
                $event->stopPropagation();
            }
        }
        if (!$response instanceof JsonResponse && 404 == $response->getStatusCode()) {
            $this->app->getTpl()->assign('request', $event->getRequest());
            $response->setContent($this->app->getTpl()->fetch('error.404'));
        }
    }

    /**
     * If result is image... This needing for captcha
     * @param KernelEvent $event
     */
    public function onKernelResponseImage(KernelEvent $event)
    {
        if (is_resource($event->getResult()) && imageistruecolor($event->getResult())) {
            $event->getResponse()->headers->set('Content-type', 'image/png');
            imagepng($event->getResult());
            $event->stopPropagation();
        }
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
            array(
                'name'=> 'Сервис',
                'sub' => array(
                    array(
                        'name'  => 'Менеджер файлов',
                        'url'   => 'elfinder/finder',
                        'class' => 'filemanager',
                    ),
                ),
            ),
            //            array(
            //                'name'  => 'Архивация базы',
            //                'url'   => '/_runtime/sxd',
            //                'class' => 'dumper',
            //            ),
            //    array(
            //        'name' => 'Система',
            //        'sub' => array(
            //            array(
            //                'name'  => 'Маршруты',
            //                'url'   => 'routes/admin',
            //            ),
            //            array(
            //                'name'  => 'Конфигурация системы',
            //                'url'   => 'system',
            //            ),
            //            array(
            //                'name'  => 'Настройка',
            //                'url'   => 'setting/admin',
            //            ),
            //            array(
            //                'name'  => 'Генератор',
            //                'url'   => 'generator',
            //            ),
            //        ),
            //    ),
            array(
                'name'  => 'Выход',
                'url'   => 'user/logout',
            ),
        );
    }
}

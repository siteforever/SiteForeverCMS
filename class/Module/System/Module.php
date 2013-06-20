<?php
/**
 * Модуль системы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\System;

use Sfcms\Kernel\KernelEvent;
use Sfcms\Model;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
                'elfinder'  => array(),
                'generator' => array(),
                'log'       => array(),
                'routes'    => array(),
                'system'    => array(),
                'setting'   => array(),
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

    public function registerService(ContainerBuilder $container)
    {
        // Mail transport defintion
        switch ($container->getParameter('mailer.transport')) {
            case 'smtp':
                $container->register('mailer.transport', 'Swift_SmtpTransport')
                    ->addArgument('%mailer.host%')
                    ->addArgument('%mailer.port%')
                    ->addArgument('%mailer.security%')
                    ->addMethodCall('setUsername', array('%mailer.username%'))
                    ->addMethodCall('setPassword', array('%mailer.password%'))
                ;
                break;
            case 'gmail':
                http://stackoverflow.com/a/4691183/2090796
                $container->register('mailer.transport', 'Swift_SmtpTransport')
                    ->addArgument('smtp.gmail.com')
                    ->addArgument(465)
                    ->addArgument('ssl')
                    ->addMethodCall('setUsername', array('%mailer.username%'))
                    ->addMethodCall('setPassword', array('%mailer.password%'))
                    ->addMethodCall('setAuthMode', array('%login%'))
                ;
                break;
            case 'null':
                $container->register('mailer.transport', 'Swift_NullTransport');
                break;
            default:
                $container->register('mailer.transport', 'Swift_SendmailTransport');
        }
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
        if (403 == $response->getStatusCode()) {
            if (!$this->app->getAuth()->isLogged()) {
                $response = new RedirectResponse($this->app->getRouter()->createLink('user/login'));
                $event->setResponse($response);
                $event->stopPropagation();
            }
        }
        if (404 == $response->getStatusCode()) {
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
            //            array(
            //                'name'  => 'Архивация базы',
            //                'url'   => '/_runtime/sxd',
            //                'class' => 'dumper',
            //            ),
                    array(
                        'name'  => 'Поиск',
                        'url'   => 'search/admin',
                    ),
                )
            ),
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

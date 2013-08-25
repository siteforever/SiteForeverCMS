<?php
/**
 * Module Purifer
 * @generator SiteForeverGenerator
 */

namespace Module\Purifer;

use Sfcms\Kernel\KernelEvent;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfModule
{
    /**
     * @inheritdoc
     */
    public function init()
    {
//        $purifier = $this->getPurifer();
//        $this->app->getEventDispatcher()->addListener('kernel.response',
//            function(KernelEvent $event) use ($purifier) {
//                $event->getResponse()->setContent(
//                    $purifier->purify($event->getResponse()->getContent())
//                );
//            }, -10);
        $this->app->getEventDispatcher()->addListener('kernel.response',
            function(KernelEvent $event) {
                $event->getResponse()->setContent(
                    preg_replace(array('/ *?\n/', '/'.PHP_EOL.'+/'), PHP_EOL, $event->getResponse()->getContent())
                );
            }, -100);
//        if (class_exists('tidy', false)) {
//            $this->app->getEventDispatcher()->addListener('kernel.response',
//                function(KernelEvent $event){
//                    $tidy = new \tidy();
//                    $tidy->parseString(
//                        $event->getResponse()->getContent(),
//                        array(
//                            'indent'         => true,
//                            'output-html'    => true,
//                            'wrap'           => 500,
//                            'doctype'        => 'user',
//                        ),
//                        'utf8'
//                    );
//                    $tidy->cleanRepair();
//                    $event->getResponse()->setContent((string)$tidy);
//                }, -10);
//        }
    }

    /**
     * @return \HTMLPurifier
     */
    public function getPurifer()
    {
        return $this->app->getContainer()->get('purifier');
    }

    /**
     * @inheritdoc
     */
    public function registerService(ContainerBuilder $container)
    {
        if (!defined('HTMLPURIFIER_PREFIX')) {
            define('HTMLPURIFIER_PREFIX', ROOT . '/vendor/spekkionu/htmlpurifier');
        }
        $container->register('purifier', 'HTMLPurifier');
    }

    /**
     * @inheritdoc
     */
    public function config()
    {
        return array(
//            'controllers' => array(
//                'SomeName' => arra    y( 'class' => 'Controller\NameController', ),
//            ),
//            'models' => array(
//                'SomeName' => 'Module\Purifer\Model\SomeModel',
//            ),
        );
    }

    /**
     * @inheritdoc
     */
    public function admin_menu()
    {
        return array(
//            array(
//                'name'  => 'Purifer',
//                'url'   => 'admin/purifer',
//            )
        );
    }
}

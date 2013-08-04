<?php
/**
 * Module Monolog
 * @generator SiteForeverGenerator
 */

namespace Module\Monolog;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfModule
{
    public function init()
    {
    }

    public function registerService(ContainerBuilder $container)
    {
        $config = $container->getParameter('monolog');
        if (isset($config['handlers'])) {
            foreach($config['handlers'] as $name => $handler) {
                switch ($handler['type']) {
                    case 'rotating':
                        $handler = $handler + array('max'=>0, 'level'=>Logger::DEBUG);
                        $handler['path'] = ROOT . '/' . trim($handler['path'], '/ ');
                        $container->set($name, new RotatingFileHandler($handler['path'], $handler['max'], $handler['level']));
                        break;
                    case 'firephp':
                        $handler = $handler + array('level'=>Logger::DEBUG);
                        $container->set($name, new FirePHPHandler($handler['level']));
                        break;
                }
            }
        }
    }


    /**
     * Return array config of module
     * @return array
     */
    public function config()
    {
        return array(
//            'controllers' => array(
//                'SomeName' => array( 'class' => 'Controller\NameController', ),
//            ),
//            'models' => array(
//                'SomeName' => 'Module\Monolog\Model\SomeModel',
//            ),
        );
    }

    public function admin_menu()
    {
        return array(
//            array(
//                'name'  => 'Monolog',
//                'url'   => 'admin/monolog',
//            )
        );
    }
}

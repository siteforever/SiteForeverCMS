<?php
/**
 * Module Monolog
 * @generator SiteForeverGenerator
 */

namespace Module\Monolog;

use Module\Monolog\DependencyInjection\LoggerExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfModule
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new LoggerExtension());
    }


    /**
     * Return array config of module
     * @return array
     */
    public function config()
    {
        return array(
        );
    }

    public function admin_menu()
    {
        return array(
        );
    }
}

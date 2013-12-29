<?php
/**
 * Robokassa
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Robokassa;

use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Module\Robokassa\DependencyInjection\RobokassaExtension;

class Module extends SfModule
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new RobokassaExtension());
    }
}

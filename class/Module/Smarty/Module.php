<?php
/**
 * Smarty Module
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Smarty;

use Module\Smarty\DependencyInjection\SmartyExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfModule
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new SmartyExtension());
    }

}

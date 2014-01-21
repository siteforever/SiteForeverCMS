<?php
/**
 * This file is part of the SiteForever package.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Smarty\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registering tagged service with tag `smarty.plugin`
 * Class RegisterPluginsPass
 * @package Module\Smarty\DependencyInjection\Compiler
 */
class RegisterPluginsPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $list = $container->findTaggedServiceIds('smarty.plugin');
        $smartyDifinition = $container->getDefinition('smarty');

        foreach ($list as $serviceId => $tags) {
            $serviceReference = new Reference($serviceId);
            foreach ($tags as $tag) {
                $smartyDifinition->addMethodCall(
                    'registerPlugin',
                    array($tag['type'], $tag['plugin'], array($serviceReference, $tag['method']))
                );
            }
        }
    }
}

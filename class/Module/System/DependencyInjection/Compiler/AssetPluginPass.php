<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AssetPluginPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('smarty.plugin.asset')) {
            return;
        }
        $definition = $container->getDefinition('smarty.plugin.asset');
        $definition->addMethodCall('addScope', ['root', $container->getParameter('root')]);
        $definition->addMethodCall('addScope', ['theme', $container->getParameter('root') . '/themes/'
            . $container->getParameter('template_theme')]);
    }
}

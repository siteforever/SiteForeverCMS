<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Smarty\DependencyInjection\Compiler;

use Sfcms\Module;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResolveViewPathPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('template');

        $modules = $container->getParameter('kernel.modules');
        $tplDefinition = $container->getDefinition('tpl');

        $theme  = $config['theme'];
        $themeCat = $container->getParameter('kernel.root_dir') . "/../themes/{$theme}/templates";

        if (is_dir($themeCat)) {
            $tplDefinition->addMethodCall('addTplDir', array($themeCat));
        } else {
            throw new \RuntimeException(sprintf('Theme "%s" not found', $theme));
        }

        /** @var string $moduleClass */
        foreach($modules as $moduleName => $moduleClass) {
            $reflectionClass = new \ReflectionClass($moduleClass);
            $path = dirname($reflectionClass->getFileName());

            if (is_dir($path . '/View')) {
                $tplDefinition->addMethodCall('addTplDir', array($path . '/View'));
            }
            if (is_dir($path . '/Widget')) {
                $tplDefinition->addMethodCall('addWidgetsDir', array($path . '/Widget'));
            }
        }

        // Registering service-plugins
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

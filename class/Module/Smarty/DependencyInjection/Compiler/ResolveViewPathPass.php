<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Smarty\DependencyInjection\Compiler;


use Sfcms\Kernel\AbstractKernel;
use Sfcms\Module;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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

        /** @var AbstractKernel $kernel */
        $kernel = $container->get('app');

        $modules = $kernel->getModules();
        $tplDefinition = $container->getDefinition('tpl');

        $theme  = $config['theme'];
        $themeCat = ROOT . "/themes/{$theme}/templates";

        if (is_dir($themeCat)) {
            $tplDefinition->addMethodCall('addTplDir', array($themeCat));
        } else {
            throw new \RuntimeException(sprintf('Theme "%s" not found', $theme));
        }

        /** @var Module $module */
        foreach($modules as $module) {
            $path = $module->getPath();
            if (is_dir($path . '/View')) {
                $tplDefinition->addMethodCall('addTplDir', array($path . '/View'));
            }
            if (is_dir($path . '/Widget')) {
                $tplDefinition->addMethodCall('addWidgetsDir', array($path . '/Widget'));
            }
        }
    }
}

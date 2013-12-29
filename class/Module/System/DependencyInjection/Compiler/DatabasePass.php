<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection\Compiler;


use Sfcms\Kernel\AbstractKernel;
use Sfcms\Module;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DatabasePass
 *
 * Registering Models class as services and registering their as subscribers,
 * if they supported EventSubscriberInterface
 *
 * @package Module\System\DependencyInjection\Compiler
 */
class DatabasePass implements CompilerPassInterface
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
        /** @var AbstractKernel $kernel */
        $kernel = $container->get('kernel');

        $modules = $kernel->getModules();

        /** @var Module $module */
        foreach($modules as $module) {
            $config = $module->config();
            if ($config && isset($config['models'])) {
                foreach($config['models'] as $name => $className) {
                    $definition = new Definition($className);
                    $container->setDefinition(sprintf('Mapper.%s', $name), $definition);
                    $container->setAlias(sprintf('Mapper.%s', $className), sprintf('Mapper.%s', $name));
                    $reflectionClass = new \ReflectionClass($className);
                    $dispatcher = $container->getDefinition('event.dispatcher');
                    if ($reflectionClass->implementsInterface('Symfony\Component\EventDispatcher\EventSubscriberInterface')) {
                        $dispatcher->addMethodCall('addSubscriber', array(new Reference(sprintf('Mapper.%s', $name))));
                    }
                }
            }
        }
    }
}

<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database\DependencyInjection\Compiler;


use Sfcms\Data\DataManager;
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
        $dispatcher = $container->getDefinition('event.dispatcher');
        /** @var DataManager $manager */
        $manager = $container->get('data.manager');

        foreach ($manager->getModelList() as $config) {
            $definition = new Definition($config['class']);
            $definition->setLazy(true);
            $container->setDefinition($config['id'], $definition);
            $reflectionClass = new \ReflectionClass($config['class']);
            if ($reflectionClass->implementsInterface('Symfony\Component\EventDispatcher\EventSubscriberInterface')) {
                $dispatcher->addMethodCall('addSubscriber', array(new Reference($config['id'])));
            }
        }
    }
}

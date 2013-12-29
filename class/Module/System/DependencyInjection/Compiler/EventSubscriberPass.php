<?php
/**
 * Register services with tag "event.subscriber" as subscribers
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EventSubscriberPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('event.dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('event.dispatcher');
        $taggedServices = $container->findTaggedServiceIds('event.subscriber');
        foreach ($taggedServices as $serviceId => $params) {
            $definition->addMethodCall('addSubscriber', array(new Reference($serviceId)));
        }

        $taggedServices = $container->findTaggedServiceIds('event.listener');
        foreach ($taggedServices as $serviceId => $params) {
            $definition->addMethodCall('addListener', array(
                $params[0]['event'],
                array(new Reference($serviceId), $params[0]['method'])),
                isset($params[0]['priority']) ? $params[0]['priority'] : 0
            );
        }
    }
}

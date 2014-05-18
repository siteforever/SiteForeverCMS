<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Doctrine\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DoctrinePass implements CompilerPassInterface
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
        $debug = $container->getParameter('debug');
        if ($debug) {
            $container->setAlias('doctrine.cache', 'doctrine.cache.array');
        } else {
            $container->setAlias('doctrine.cache', 'doctrine.cache.filesystem');
        }

        $configuration = $container->getDefinition('doctrine.configuration');
        $configuration->addMethodCall('setMetadataCacheImpl', [new Reference('doctrine.cache')]);
        $configuration->addMethodCall('setQueryCacheImpl', [new Reference('doctrine.cache')]);
        $configuration->addMethodCall('setResultCacheImpl', [new Reference('doctrine.cache')]);
        $configuration->addMethodCall('setProxyDir', [
                sprintf('%s/runtime/cache/%s/proxy', $container->getParameter('root'), $container->getParameter('env'))
            ]);
        $configuration->addMethodCall('setAutoGenerateProxyClasses', [$debug]);

        $container->getDefinition('pdo')->addMethodCall('setAttribute', [13, ['Doctrine\DBAL\Driver\PDOStatement', []]]);
    }
}

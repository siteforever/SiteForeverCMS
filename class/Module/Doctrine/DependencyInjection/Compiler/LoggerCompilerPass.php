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

class LoggerCompilerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('db_channel')) {
            return;
        }
        $sqlLogger = new Definition('Module\Doctrine\Logger\DoctrineLogger', [new Reference('db_channel')]);
        $container->setDefinition('doctrine.sql.logger', $sqlLogger);
        $container->getDefinition('doctrine.configuration')->addMethodCall('setSQLLogger', [new Reference('doctrine.sql.logger')]);
    }

}

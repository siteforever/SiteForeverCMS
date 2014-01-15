<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Monolog\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('logger');
        $rootNode
            ->children()
                ->arrayNode('handlers')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->scalarNode('path')->defaultValue(null)->end()
                            ->integerNode('max')->defaultValue(0)->end()
                            ->integerNode('level')->defaultValue(Logger::DEBUG)->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

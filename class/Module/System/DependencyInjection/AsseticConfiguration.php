<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class AsseticConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('assetic');
        $rootNode
            ->children()
                ->booleanNode('debug')->defaultValue('%debug%')->end()
                ->booleanNode('bootstrap')->defaultValue(false)->end()
                ->scalarNode('output')->defaultValue('%root%/static')->end()
                // assets
                ->arrayNode('assets')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('output')->end()
                            ->arrayNode('input')->requiresAtLeastOneElement()->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
        return $treeBuilder;
    }
}

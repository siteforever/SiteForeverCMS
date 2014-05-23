<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Gallery\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $rootNode = $treeBuilder->root('gallery');
        $rootNode
            ->children()
                ->scalarNode('path')->defaultValue('/files/gallery')->end()
                ->integerNode('max_file_size')->defaultValue(2 * 1024 * 1024)->end()
                ->arrayNode('mime')
                    ->prototype('scalar')
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}

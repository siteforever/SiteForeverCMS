<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\DependencyInjection;

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
        $rootNode = $treeBuilder->root('catalog');
        $rootNode
            ->children()
                ->integerNode('level')->defaultValue(0)->end()
                ->integerNode('onPage')->defaultValue(10)->end()
                ->scalarNode('gallery_dir')->defaultValue('/files/catalog/gallery')->end()
                ->scalarNode('order_default')->defaultValue('name')->end()
                ->arrayNode('order_list')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ->end()
        ;
        // 1 - добавление полей
        // 2 - обрезание лишнего

        return $treeBuilder;
    }
}

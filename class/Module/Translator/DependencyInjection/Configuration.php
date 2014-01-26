<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Translator\DependencyInjection;

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
        $rootNode = $treeBuilder->root('translator');
        $rootNode
            ->children()
                ->scalarNode('locale')->defaultValue('en')->end()
                ->scalarNode('fallback')->defaultValue('en')->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}

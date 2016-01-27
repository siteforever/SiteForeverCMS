<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Smarty\DependencyInjection;

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
        $rootNode = $treeBuilder->root('template');
        $rootNode
            ->children()
                ->arrayNode('parameters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('theme')->isRequired()->end()
                        ->scalarNode('ext')->defaultValue('tpl')->end()
                        ->booleanNode('compile_check')->defaultValue("%debug%")->end()
                        ->booleanNode('force_compile')->defaultFalse()->end()
                        ->booleanNode('caching')->defaultFalse()->end()
                        ->arrayNode('cache')
                            ->children()
                                ->scalarNode('livetime')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ;
        return $treeBuilder;
    }
}

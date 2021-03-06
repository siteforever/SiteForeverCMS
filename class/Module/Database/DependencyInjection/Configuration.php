<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database\DependencyInjection;

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
        $rootNode = $treeBuilder->root('database');
        $rootNode
            ->children()
                ->scalarNode('dsn')->defaultValue('mysql:host=%db_host%;port=%db_port%;dbname=%db_name%')->end()
                ->scalarNode('name')->defaultValue('%db_name%')->end()
                ->scalarNode('login')->defaultValue('%db_login%')->end()
                ->scalarNode('password')->defaultValue('%db_password%')->end()
                ->arrayNode('options')->prototype('scalar')->end()->end()
                ->booleanNode('debug')->defaultValue('%db_debug%')->end()
                ->booleanNode('log')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}

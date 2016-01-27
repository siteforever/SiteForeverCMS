<?php
/**
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Doctrine\DependencyInjection;

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
        $rootNode = $treeBuilder->root('doctrine');
        $rootNode
            ->children()
                ->booleanNode('debug')->defaultTrue()->end()
                ->arrayNode('connection')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('driver')
                            ->values(['pdo_mysql', 'mysqli', 'drizzle_pdo_mysql'])
                            ->defaultValue('pdo_mysql')
                        ->end()
                        ->scalarNode('dbname')->end()
                        ->scalarNode('charset')->defaultValue('utf8')->end()
                        ->scalarNode('user')->defaultValue('root')->end()
                        ->scalarNode('password')->defaultValue('~')->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->integerNode('port')->defaultValue(3306)->end()
                        ->scalarNode('unix_socket')->end()
                        ->scalarNode('path')->end()
                        ->booleanNode('memory')->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('metadata')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('type')->values(['redis','array','filesystem','memcached'])->defaultValue('array')->end()
                                ->scalarNode('path')->defaultValue('%sfcms.cache_dir%/dbal/metadata')->end()
                            ->end()
                        ->end()
                        ->arrayNode('query')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('type')->values(['redis','array','filesystem','memcached'])->defaultValue('array')->end()
                                ->scalarNode('path')->defaultValue('%sfcms.cache_dir%/dbal/query')->end()
                            ->end()
                        ->end()
                        ->arrayNode('result')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('type')->values(['redis','array','filesystem','memcached'])->defaultValue('array')->end()
                                ->scalarNode('path')->defaultValue('%sfcms.cache_dir%/dbal/result')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}

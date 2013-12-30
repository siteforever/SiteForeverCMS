<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection;

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
        $rootNode = $treeBuilder->root('system');
        $rootNode
            ->children()
                ->arrayNode('session')
                    ->children()
                        ->scalarNode('handler')
                            ->isRequired()
                            ->validate()
                                ->ifNotInArray(array('native', 'pdo', null))
                                ->thenInvalid('The %s session handler is not supported')
                            ->end()
                        ->end()
                        ->scalarNode('storage')
                            ->defaultValue('handler')
                            ->validate()
                                ->ifNotInArray(array('handler', 'mock', null))
                                ->thenInvalid('The %s session storage is not supported')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}

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
                                ->ifNotInArray(array('native', 'mock', null))
                                ->thenInvalid('The %s session storage is not supported')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->enumNode('editor')
                    ->values(array('ckeditor', 'tinymce', 'elrte'))->defaultValue('ckeditor')
                ->end()
                ->scalarNode('static_base_url')
                    ->defaultValue('/static')
                ->end()
                ->scalarNode('static_app')
                    ->defaultValue('/static/system/app')
                ->end()
                ->arrayNode('admin_controllers')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('label')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}

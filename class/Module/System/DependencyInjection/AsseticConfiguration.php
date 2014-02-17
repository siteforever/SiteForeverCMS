<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Process\ExecutableFinder;

class AsseticConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $finder = new ExecutableFinder();

        $rootNode = $builder->root('assetic');
        $rootNode
            ->children()
                ->booleanNode('debug')->defaultValue('%debug%')->end()
                ->booleanNode('bootstrap')->defaultValue(false)->end()
                ->scalarNode('output')->defaultValue('%root%/static')->end()
                ->scalarNode('java')->defaultValue(function() use($finder) { return $finder->find('java', '/usr/bin/java'); })->end()
                ->scalarNode('node')->defaultValue(function() use($finder) { return $finder->find('node', '/usr/bin/node'); })->end()
                ->arrayNode('node_paths')
                ->prototype('scalar')->end()
                ->end()
                ->scalarNode('ruby')->defaultValue(function() use($finder) { return $finder->find('ruby', '/usr/bin/ruby'); })->end()
                ->scalarNode('sass')->defaultValue(function() use($finder) { return $finder->find('sass', '/usr/bin/sass'); })->end()
            ->end()

            ->children()
                ->arrayNode('filters')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('variable')
                        ->treatNullLike(array())
                        ->validate()
                            ->ifTrue(function($v) { return !is_array($v); })
                            ->thenInvalid('The assetic.filters config %s must be either null or an array.')
                        ->end()
                    ->end()
                    ->validate()
                        ->always(function($v) use ($finder) {
                            if (isset($v['compass']) && !isset($v['compass']['bin'])) {
                                $v['compass']['bin'] = $finder->find('compass', '/usr/bin/compass');
                            }
                            return $v;
                        })
                    ->end()
                ->end()
            ->end()

            ->children()
                ->arrayNode('assets')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            // a scalar is a simple formula of one input file
                            ->ifTrue(function($v) { return !is_array($v); })
                            ->then(function($v) { return array('inputs' => array($v)); })
                        ->end()
                        ->beforeNormalization()
                            ->always()
                            ->then(function($v)
                            {
                                // cast scalars as array
                                foreach (array('input', 'inputs', 'filter', 'filters') as $key) {
                                    if (isset($v[$key]) && !is_array($v[$key])) {
                                        $v[$key] = array($v[$key]);
                                    }
                                }

                                // organize arbitrary options
                                foreach ($v as $key => $value) {
                                    if (!in_array($key, array('input', 'inputs', 'filter', 'filters', 'option', 'options'))) {
                                        $v['options'][$key] = $value;
                                        unset($v[$key]);
                                    }
                                }

                                return $v;
                            })
                        ->end()

                        ->children()
                            ->arrayNode('inputs')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('filters')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('options')
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()

                        ->children()
                            ->scalarNode('output')->end()
                            ->arrayNode('input')->requiresAtLeastOneElement()->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
        return $builder;
    }
}

<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Mailer\DependencyInjection;

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
        $rootNode = $treeBuilder->root('mailer');
        $rootNode
            ->children()
                ->scalarNode('transport')->defaultValue('smtp')->end()
                ->scalarNode('username')->defaultNull()->end()
                ->scalarNode('password')->defaultNull()->end()
                ->scalarNode('host')->defaultValue('localhost')->end()
                ->scalarNode('port')->defaultFalse()->end()
                ->scalarNode('timeout')->defaultValue(30)->end()
                ->scalarNode('source_ip')->defaultNull()->end()
                ->scalarNode('encryption')
                    ->defaultNull()
                    ->validate()
                        ->ifNotInArray(array('tls', 'ssl', null))
                        ->thenInvalid('The %s encryption is not supported')
                    ->end()
                ->end()
                ->scalarNode('auth_mode')
                    ->defaultNull()
                    ->validate()
                        ->ifNotInArray(array('plain', 'login', 'cram-md5', null))
                        ->thenInvalid('The %s authentication mode is not supported')
                    ->end()
                ->end()
                ->scalarNode('sender_address')->end()
                ->scalarNode('delivery_address')->end()
                ->arrayNode('antiflood')
                    ->children()
                        ->scalarNode('threshold')->defaultValue(99)->end()
                        ->scalarNode('sleep')->defaultValue(0)->end()
                    ->end()
                ->end()
                ->arrayNode('spool')
                    ->children()
                        ->scalarNode('type')->defaultValue('file')->end()
                        ->scalarNode('path')->defaultValue('%root%/runtime/mailer/spool')->end()
                        ->scalarNode('id')->defaultNull()->info('Used by "service" type')->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($v) { return 'service' === $v['type'] && empty($v['id']); })
                        ->thenInvalid('You have to configure the service id')
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}

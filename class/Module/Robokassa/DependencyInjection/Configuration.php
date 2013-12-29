<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Robokassa\DependencyInjection;

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
        $rootNode = $treeBuilder->root('robokassa');
        $rootNode
            ->children()
                ->scalarNode('MrchLogin')->defaultValue('%robokassa_mrch_login%')->end()
                ->scalarNode('MerchantPass1')->defaultValue('%robokassa_merchant_pass1%')->end()
                ->scalarNode('MerchantPass2')->defaultValue('%robokassa_merchant_pass2%')->end()
                ->booleanNode('debug')->defaultValue('%debug%')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

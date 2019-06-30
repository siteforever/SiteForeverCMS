<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class CaptchaConfiguration  implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('captcha');
        $rootNode
            ->children()
                ->integerNode('width')->defaultValue(100)->end()
                ->integerNode('height')->defaultValue(25)->end()
                ->integerNode('color')->defaultValue(0x000000)->end()
                ->integerNode('bgcolor')->defaultValue(0xffffff)->end()
                ->scalarNode('font')->defaultValue(realpath(__DIR__ . '/../static/captcha/infroman.ttf'))->end()
                ->integerNode('length')->defaultValue(6)->end()
                ->scalarNode('html_key')->isRequired()->end()
                ->scalarNode('backend_key')->isRequired()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}

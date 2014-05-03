<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RequireJsPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('asset.service')) {
            return;
        }

        $source = __DIR__ . '/../../Static/js/vendor';
        $asseticService = $container->getDefinition('asset.service');
        $asseticService->addMethodCall('addAsseticName', array('assetic.assets.require_js'));

        $container->setParameter('assetic.assets.require_js', array(
                'inputs' => array(
                    $source . '/require.js',
                    $source . '/jquery.js',
                    $source . '/json2.js',
                    $source . '/underscore.js',
                    $source . '/backbone.js',
                    $source . '/require-config.js',
                ),
                'filters' => array('?jsmin'),
                'options' => array(
                    'output' => 'require-vendors.js',
                ),
            ));
    }
}

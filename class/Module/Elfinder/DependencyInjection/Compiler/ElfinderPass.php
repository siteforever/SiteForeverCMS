<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Elfinder\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ElfinderPass implements CompilerPassInterface
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

        $source = ROOT . '/vendor/Studio-42/elFinder/js';
        $asseticService = $container->getDefinition('asset.service');
        $asseticService->addMethodCall('addAsseticName', array('assetic.assets.elfinder_js'));
        $container->setParameter('assetic.assets.elfinder_js', array(
                'inputs' => array(
                    $source . '/elFinder.js',
                    $source . '/elFinder.version.js',
                    $source . '/jquery.elfinder.js',
                    $source . '/elFinder.resources.js',
                    $source . '/elFinder.options.js',
                    $source . '/elFinder.history.js',
                    $source . '/elFinder.command.js',
                    $source . '/ui/*.js',
                    $source . '/commands/*.js',
                    $source . '/jquery.dialogelfinder.js',
                    $source . '/proxy/*.js',
                    __DIR__ . '/../../static/i18n/elfinder.ru.js',
                    $source . '/i18n/elfinder.en.js',
                ),
                'filters' => array('?jsmin'),
                'options' => array(
                    'output' => 'elfinder.js',
                ),
            ));

        $asseticService->addMethodCall('addAsseticName', array('assetic.assets.elfinder_css'));
        $container->setParameter('assetic.assets.elfinder_css', array(
                'inputs' => array(
                    $source . '/../css/*.css',
                ),
                'filters' => array('?cssmin'),
                'options' => array(
                    'output' => 'elfinder.css',
                ),
            ));
    }
}

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

        $asseticService = $container->getDefinition('asset.service');
        $asseticService->addMethodCall('addAsseticName', ['assetic.assets.require_js']);

        $container->setParameter('assetic.assets.require_js', array(
                'inputs' => array(
                    __DIR__ . '/../../static/vendor/jquery-1.11.1.js',
                    __DIR__ . '/../../static/vendor/jquery-ui.js',
//                    __DIR__ . '/../../static/vendor/jquery-ui/i18n/datepicker-ru.js',
                    __DIR__ . '/../../static/vendor/underscore.js',
                    __DIR__ . '/../../static/vendor/backbone.js',
                    __DIR__ . '/../../static/vendor/twbs/js/bootstrap.js',
//                    __DIR__ . '/../../static/vendor/define.js',
                    __DIR__ . '/../../static/app.js',
                ),
                'filters' => array('?yui_js'),
                'options' => array(
                    'output' => 'static/cms/app.js',
                ),
            ));
    }
}

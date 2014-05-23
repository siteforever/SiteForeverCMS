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

        $root = $container->getParameter('root');
        $asseticService = $container->getDefinition('asset.service');
        $asseticService->addMethodCall('addAsseticName', ['assetic.assets.require_js']);

        $container->setParameter('assetic.assets.require_js', array(
                'inputs' => array(
                    $root . '/components/require.js',
                    $root . '/components/jquery/jquery.js',
                    $root . '/components/jquery-ui/ui/i18n/jquery.ui.datepicker-ru.js',
                    $root . '/components/jquery-ui/jquery-ui-built.js',
                    $root . '/components/underscore/underscore-built.js',
                    $root . '/components/backbone/backbone-built.js',
                    $root . '/components/bootstrap/bootstrap-built.js',
                    $root . '/static/system/admin/define.js',
                ),
                'filters' => array('?yui_js'),
                'options' => array(
                    'output' => 'static/require-vendors.js',
                ),
            ));
    }
}

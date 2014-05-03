<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\JqGrid\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JqGridPass implements CompilerPassInterface
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

        $source = ROOT . '/vendor/tonytomov/jqGrid';
        $asseticService = $container->getDefinition('asset.service');
        $asseticService->addMethodCall('addAsseticName', array('assetic.assets.jqgrid_js'));

        $container->setParameter('assetic.assets.jqgrid_js', array(
                'inputs' => array(
                    $source . '/js/i18n/grid.locale-ru.js',
                    $source . '/js/grid.base.js',
                    $source . '/js/grid.common.js',
                    $source . '/js/grid.formedit.js',
                    $source . '/js/grid.inlinedit.js',
                    $source . '/js/grid.celledit.js',
                    $source . '/js/grid.subgrid.js',
                    $source . '/js/grid.treegrid.js',
                    $source . '/js/grid.grouping.js',
                    $source . '/js/grid.custom.js',
                    $source . '/js/grid.tbltogrid.js',
                    $source . '/js/grid.import.js',
                    $source . '/js/jquery.fmatter.js',
                    $source . '/js/JsonXml.js',
                    $source . '/js/grid.jqueryui.js',
                    $source . '/js/grid.filter.js',
                ),
                'filters' => array('?jsmin'),
                'options' => array(
                    'output' => 'admin/jquery/jqgrid/jqgrid.js',
                ),
            ));

        $asseticService->addMethodCall('addAsseticName', array('assetic.assets.jqgrid_css'));
        $container->setParameter('assetic.assets.jqgrid_css', array(
                'inputs' => array(
                    $source . '/css/ui.jqgrid.css',
                ),
                'filters' => array('?cssmin'),
                'options' => array(
                    'output' => 'admin/jquery/jqgrid/ui.jqgrid.css',
                ),
            ));
    }
}

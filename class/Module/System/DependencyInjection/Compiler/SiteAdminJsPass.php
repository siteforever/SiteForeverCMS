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

class SiteAdminJsPass implements CompilerPassInterface
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

        $source = __DIR__ . '/../../Static/js';
        $asseticService->addMethodCall('addAsseticName', array('assetic.assets.site_js'));

        $container->setParameter('assetic.assets.site_js', array(
                'inputs' => array(
                    $source . '/site/site.js',
//                    $source . '/jquery/fancybox/jquery.fancybox-1.3.1.js',
                    $source . '/jquery/jquery.form.js',
                    $source . '/jquery/jquery.gallery.js',
                    $source . '/jquery/jquery.captcha.js',
                    $source . '/module/console.js',
                    $source . '/module/basket.js',
                    $source . '/module/behavior.js',
                    $source . '/module/catalog.js',
                    $source . '/module/form.js',
                    $source . '/module/alert.js',
                    $source . '/site/site-define.js',
                ),
                'filters' => array('?jsmin'),
                'options' => array(
                    'output' => 'site.js',
                ),
            ));


        $source = __DIR__ . '/../../Static/js/admin';
        $asseticService->addMethodCall('addAsseticName', array('assetic.assets.admin_js'));

        $container->setParameter('assetic.assets.admin_js', array(
                'inputs' => array(
                    $source . '/admin.js',
                    $source . '/jquery/*.js',
                    $source . '/catalog/*.js',
                    '@site_js',
                    $source . '/app.js',
                ),
                'filters' => array('?jsmin'),
                'options' => array(
                    'output' => 'admin.js',
                ),
            ));
    }
}

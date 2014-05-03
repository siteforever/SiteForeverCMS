<?php
/**
 * Module {$name}
 * @generator SiteForeverGenerator
 */

namespace {$ns};

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new {$ns}Extension());
    }

    /**
     * Return array config of module
     * @return array
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'SomeName' => array( 'class' => 'Controller\NameController', ),
            ),
            'models' => array(
                'SomeName' => '{$ns}\Model\SomeModel',
            ),
        );
    }
}

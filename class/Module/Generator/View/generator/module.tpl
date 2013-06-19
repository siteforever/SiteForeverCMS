<?php
/**
 * Module {$name}
 * @generator SiteForeverGenerator
 */

namespace {$ns};

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    public function init()
    {
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

    public function admin_menu()
    {
        return array(
            array(
                'name'  => '{$name}',
                'url'   => 'admin/{$name|strtolower}',
            )
        );
    }
}

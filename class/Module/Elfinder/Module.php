<?php
/**
 * Module Elfinder
 * @generator SiteForeverGenerator
 */

namespace Module\Elfinder;

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
                'elfinder' => array(),
            ),
//            'models' => array(
//                'SomeName' => 'Module\Elfinder\Model\SomeModel',
//            ),
        );
    }

    public function admin_menu()
    {
        return array(
//            array(
//                'name'  => 'Elfinder',
//                'url'   => 'admin/elfinder',
//            )
        );
    }
}

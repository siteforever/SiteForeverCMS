<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database;

use Module\Database\DependencyInjection\Compiler\DatabasePass;
use Module\Database\DependencyInjection\DatabaseExtension;
use Sfcms\Module as SfcmsModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfcmsModule
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new DatabaseExtension());
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DatabasePass());
    }

}

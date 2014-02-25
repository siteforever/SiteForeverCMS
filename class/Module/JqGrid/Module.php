<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\JqGrid;

use Module\JqGrid\DependencyInjection\Compiler\JqGridPass;
use Module\JqGrid\DependencyInjection\JqGridExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends \Sfcms\Module
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new JqGridExtension());
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new JqGridPass());
    }
}

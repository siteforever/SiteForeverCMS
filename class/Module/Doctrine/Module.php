<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Doctrine;

//use Module\Doctrine\DependencyInjection\Compiler\DoctrinePass;
use Module\Doctrine\DependencyInjection\Compiler\LoggerCompilerPass;
use Module\Doctrine\DependencyInjection\DoctrineExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfModule
{
    public function build(ContainerBuilder $container)
    {
        $container->registerExtension(new DoctrineExtension());
        $container->addCompilerPass(new LoggerCompilerPass());
    }
}

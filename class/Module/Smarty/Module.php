<?php
/**
 * Smarty Module
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Smarty;

use Module\Smarty\DependencyInjection\Compiler\CaptchaParamsPass;
use Module\Smarty\DependencyInjection\Compiler\RegisterPluginsPass;
use Module\Smarty\DependencyInjection\Compiler\ResolveViewPathPass;
use Module\Smarty\DependencyInjection\SmartyExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfModule
{
    public function build(ContainerBuilder $container)
    {
        $container->registerExtension(new SmartyExtension());
        $container->addCompilerPass(new ResolveViewPathPass());
        $container->addCompilerPass(new RegisterPluginsPass());
        $container->addCompilerPass(new CaptchaParamsPass());
    }
}

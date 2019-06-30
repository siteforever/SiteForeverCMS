<?php


namespace Module\Smarty\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CaptchaParamsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $captcha = $container->getParameter('captcha');
        $smartyDifinition = $container->getDefinition('smarty');
        $smartyDifinition->addMethodCall('assign', ['captcha', $captcha]);
    }
}

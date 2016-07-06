<?php

namespace Siteforever\Bundle\CmsBundle;

use Siteforever\Bundle\CmsBundle\DependencyInjection\Compiler\RegisterTranslatesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiteforeverCmsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterTranslatesPass());
    }
}

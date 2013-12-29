<?php
/**
 * Module Mailer
 * @generator SiteForeverGenerator
 */

namespace Module\Mailer;

use Module\Mailer\DependencyInjection\MailerExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfModule
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new MailerExtension());
    }

//    public function build(ContainerBuilder $container)
//    {
//        $container->addCompilerPass(new ChooseTransportPass());
//    }

    /**
     * Return array config of module
     * @return array
     */
    public function config()
    {
        return array(
        );
    }

}

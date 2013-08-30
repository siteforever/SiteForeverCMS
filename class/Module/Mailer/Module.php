<?php
/**
 * Module Mailer
 * @generator SiteForeverGenerator
 */

namespace Module\Mailer;

use Sfcms\Module as SfModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        );
    }

    public function registerService(ContainerBuilder $container)
    {
        // Mail transport defintion
        switch (strtolower($container->getParameter('mailer_transport'))) {
            case 'smtp':
                $container->register('mailer_transport', 'Swift_SmtpTransport')
                    ->addArgument('%mailer_host%')
                    ->addArgument('%mailer_port%')
                    ->addArgument('%mailer_security%')
                    ->addMethodCall('setUsername', array('%mailer_username%'))
                    ->addMethodCall('setPassword', array('%mailer_password%'))
                ;
                break;
            case 'gmail':
//                http://stackoverflow.com/a/4691183/2090796
                $container->register('mailer_transport', 'Swift_SmtpTransport')
                    ->addArgument('smtp.gmail.com')
                    ->addArgument(465)
                    ->addArgument('ssl')
                    ->addMethodCall('setUsername', array('%mailer_username%'))
                    ->addMethodCall('setPassword', array('%mailer_password%'))
                    ->addMethodCall('setAuthMode', array('login'))
                ;
                break;
            case 'null':
                $container->register('mailer_transport', 'Swift_NullTransport');
                break;
            case 'sendmail':
                $container->register('mailer_transport', 'Swift_SendmailTransport');
                break;
            default:
                $container->register('mailer_transport', 'Swift_MailTransport');
        }
    }

}

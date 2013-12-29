<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Mailer\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class MailerExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/..'));
        $loader->load('config.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter($this->getAlias(), $config);

        $smtpDefenition = new Definition('Swift_SmtpTransport', array($config['host'], $config['port'], $config['encryption']));
        $smtpDefenition->addMethodCall('setUsername', array($config['username']));
        $smtpDefenition->addMethodCall('setPassword', array($config['password']));
        $smtpDefenition->addMethodCall('setAuthMode', array($config['auth_mode']));
        $container->setDefinition('swift_smtp_transport', $smtpDefenition);

        $gmailDefenition = new Definition('Swift_SmtpTransport', array("smtp.gmail.com", "465", "ssl"));
        $gmailDefenition->addMethodCall('setUsername', array($config['username']));
        $gmailDefenition->addMethodCall('setPassword', array($config['password']));
        $gmailDefenition->addMethodCall('setAuthMode', array('login'));
        $container->setDefinition('swift_gmail_transport', $gmailDefenition);

        $container->setAlias('mailer_transport', sprintf('swift_%s_transport', $config['transport'] ? $config['transport'] : 'null'));
        $container->setDefinition('swift_mailer', new Definition('Swift_Mailer', array(new Reference('mailer_transport'))));
        $container->setAlias('mailer', 'swift_mailer');
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     *
     * @api
     */
    public function getAlias()
    {
        return 'mailer';
    }

}

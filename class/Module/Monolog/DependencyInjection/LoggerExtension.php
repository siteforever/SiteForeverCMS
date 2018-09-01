<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Monolog\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class LoggerExtension extends Extension
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

        $handlersReferences = array();
        if (isset($config['handlers'])) {
            foreach($config['handlers'] as $name => $handler) {
                switch ($handler['type']) {
                    case 'rotating':
                        $container->setDefinition($name, new Definition(
                            'Monolog\Handler\RotatingFileHandler',
                            array($handler['path'], $handler['max'], $handler['level']))
                        );
                        break;
                    case 'firephp':
                        $container->setDefinition($name, new Definition('Monolog\Handler\FirePHPHandler', array($handler['level'])));
                        break;
                }
                $handlersReferences[] = new Reference($name);
            }
        }

        $dbChannelDefinion = $container->getDefinition('db_logger');
        $dbChannelDefinion->setArguments(array('db', $handlersReferences));

        $channelDefinion = $container->getDefinition('logger');
        $channelDefinion->setArguments(array('log', $handlersReferences));
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
        return 'logger';
    }

}

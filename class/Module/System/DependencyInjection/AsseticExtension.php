<?php
/**
 * Extension for static source publication
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AsseticExtension implements ExtensionInterface
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
        $loader->load('assetic.yml');

        $configuration = new AsseticConfiguration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);
        $container->setParameter($this->getAlias(), $config);
        foreach($config as $key => $value) {
            $container->setParameter($this->getAlias() . '.' . $key, $value);
        }
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
        return false;
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
        return 'assetic';
    }

}

<?php
/**
 * Extension for static source publication
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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

        $xmlLoader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../config'));

        $configuration = new AsseticConfiguration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);
        $container->setParameter($this->getAlias(), $config);

        $container->setParameter('assetic.bootstrap', $config['bootstrap']);
        $container->setParameter('assetic.output', $config['output']);
        $container->setParameter('assetic.debug', $config['debug']);
        $container->setParameter('assetic.java.bin', $config['java']);
        $container->setParameter('assetic.node.bin', $config['node']);
        $container->setParameter('assetic.node.paths', $config['node_paths']);
        $container->setParameter('assetic.ruby.bin', $config['ruby']);
        $container->setParameter('assetic.sass.bin', $config['sass']);

        $filterManager = $container->getDefinition('asset.filter.manager');
        $asseticService = $container->getDefinition('assetic_service');

        // register filters
        foreach ($config['filters'] as $name => $filter) {
            if (isset($filter['resource'])) {
                $xmlLoader->load($container->getParameterBag()->resolveValue($filter['resource']));
                unset($filter['resource']);
            } else {
                $xmlLoader->load('filters/'.$name.'.xml');
            }

            if (isset($filter['file'])) {
                $container->getDefinition('assetic.filter.'.$name)->setFile($filter['file']);
                unset($filter['file']);
            }

            $filterManager->addMethodCall('set', array($name, new Reference('assetic.filter.'.$name)));

            foreach ($filter as $key => $value) {
                $container->setParameter('assetic.filter.'.$name.'.'.$key, $value);
            }
        }

        foreach ($config['assets'] as $name => $assets) {
            $container->setParameter('assetic.assets.' . $name, $assets);
            $asseticService->addMethodCall('addAsseticName', array($this->getAlias() . '.assets.' . $name));
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

<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Smarty\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SmartyExtension extends Extension
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

        $smarty = $container->getDefinition('smarty');
        $smarty->addMethodCall('assign', ['form_template', $config['parameters']['form']]);
        $smarty->setProperty('compile_check', $config['parameters']['compile_check']);
        $smarty->setProperty('force_compile', $config['parameters']['force_compile']);
        $smarty->setProperty('caching', $config['parameters']['caching']);

        $container->setParameter($this->getAlias(), $config['parameters']);
        foreach ($config['parameters'] as $key => $val) {
            $container->setParameter(sprintf('%s.%s', $this->getAlias(), $key), $val);
        }
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
        return 'template';
    }

}

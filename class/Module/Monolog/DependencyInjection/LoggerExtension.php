<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Monolog\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Monolog\Logger;

class LoggerExtension implements ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        if (isset($config[0]['handlers'])) {
            foreach($config[0]['handlers'] as $name => $handler) {
                switch ($handler['type']) {
                    case 'rotating':
                        $handler = $handler + array('max'=>0, 'level'=>Logger::DEBUG);
                        $handler['path'] = ROOT . '/' . trim($handler['path'], '/ ');
                        $dir = dirname($handler['path']);
                        if (!is_dir($dir)) {
                            @mkdir($dir, 0777, true);
                        }
                        $container->setDefinition($name, new Definition('Monolog\Handler\RotatingFileHandler', array($handler['path'], $handler['max'], $handler['level'])));
                        break;
                    case 'firephp':
                        $handler = $handler + array('level'=>Logger::DEBUG);
                        $container->setDefinition($name, new Definition('Monolog\Handler\FirePHPHandler', array($handler['level'])));
                        break;
                }
            }
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
        return 'logger';
    }

}

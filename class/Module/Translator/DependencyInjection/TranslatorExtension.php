<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Translator\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class TranslatorExtension extends Extension
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

        $locale = $config['locale'];
        $container->setDefinition(
            'translator.message.selector',
            new Definition('Symfony\Component\Translation\MessageSelector')
        );
        $transDefinition = new Definition(
            'Module\Translator\Component\TranslatorComponent',
            array($locale, new Reference('translator.message.selector'))
        );
        if (!empty($config['fallback'])) {
            $transDefinition->addMethodCall('setFallbackLocale', array(array($config['fallback'])));
        }
        $i18n = $container->getDefinition('i18n');
        $i18n->addMethodCall('setLocale', array(LC_ALL, "en_US.UTF-8", "en_US", "English", "C"));
        list($loc) = explode('_', $locale);
        switch (strtolower($loc)) {
            case 'ru':
                $i18n->addMethodCall('setLocale', array(LC_TIME & LC_MONETARY & LC_COLLATE & LC_CTYPE, "rus", "ru_RU.UTF-8", "Russia"));
                break;
        }
        $container->setDefinition('translator', $transDefinition);
    }

    public function getAlias()
    {
        return 'translator';
    }
}

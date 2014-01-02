<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Translator\DependencyInjection\Compiler;


use Sfcms\Kernel\AbstractKernel;
use Sfcms\Module;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterTranslatesPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        /** @var AbstractKernel $kernel */
        $kernel = $container->get('app');
        $modules = $kernel->getModules();
        /** @var Definition $definition */
        $definition = $container->getDefinition('translator');
        $loaders = array();

        /** @var Module $module */
        foreach($modules as $module) {
            $path = $module->getPath() . '/Translate';
            if (is_dir($path)) {
                foreach (glob($path . '/*') as $file) {
                    if (preg_match('/(?P<domain>\w+)\.(?P<lang>\w+)\.(?P<type>\w+)$/', basename($file), $match)) {
                        $match['domain'] = strtolower($match['domain']);
                        if (!isset($loaders[$match['type']])) {
                            switch ($match['type']) {
                                case 'yml':
                                    $ymlDefinition = new Definition('Symfony\Component\Translation\Loader\YamlFileLoader');
                                    $container->setDefinition('translator.yml.loader', $ymlDefinition);
                                    $definition->addMethodCall('addLoader', array('yml', new Reference('translator.yml.loader')));
                                    break;
                                case 'php':
                                    $phpDefinition = new Definition('Symfony\Component\Translation\Loader\PhpFileLoader');
                                    $container->setDefinition('translator.php.loader', $phpDefinition);
                                    $definition->addMethodCall('addLoader', array('php', new Reference('translator.php.loader')));
                                    break;
                                case 'xliff':
                                    $phpDefinition = new Definition('Symfony\Component\Translation\Loader\XliffLoader');
                                    $container->setDefinition('translator.xliff.loader', $phpDefinition);
                                    $definition->addMethodCall('addLoader', array('xliff', new Reference('translator.xliff.loader')));
                                    break;
                            }
                            $loaders[$match['type']] = true;
                        }
                        $definition->addMethodCall('addResource', array($match['type'], $file, $match['lang'], $match['domain']));
                    }
                }
            }
        }
    }
}

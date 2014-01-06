<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database\DependencyInjection\Compiler;


use Sfcms\Data\DataManager;
use Sfcms\Kernel\AbstractKernel;
use Sfcms\Module;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DatabasePass
 *
 * Registering Models class as services and registering their as subscribers,
 * if they supported EventSubscriberInterface
 *
 * @package Module\System\DependencyInjection\Compiler
 */
class DatabasePass implements CompilerPassInterface
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
        $kernel = $container->get('kernel');

        $modules = $kernel->getModules();

        $dispatcher = $container->getDefinition('event.dispatcher');
        /** @var DataManager $manager */
        $manager = $container->get('data.manager');

        foreach ($manager->getModelList() as $config) {
            $definition = new Definition($config['class']);
            $container->setDefinition($config['id'], $definition);
            $container->setAlias(sprintf('Mapper.%s', $config['class']), $config['id']);
            $reflectionClass = new \ReflectionClass($config['class']);
            if ($reflectionClass->implementsInterface('Symfony\Component\EventDispatcher\EventSubscriberInterface')) {
                $dispatcher->addMethodCall('addSubscriber', array(new Reference($config['id'])));
            }
//            if ($reflectionClass->hasMethod('onSaveStart')) {
//                $dispatcher->addMethodCall('addListener', array(
//                        sprintf('%s.save.start', $config['alias']),
//                        array(new Reference($config['id']), 'onSaveStart')
//                    ));
//            }
//            if ($reflectionClass->hasMethod('onSaveSuccess')) {
//                $dispatcher->addMethodCall('addListener', array(
//                        sprintf('%s.save.success', $config['alias']),
//                        array(new Reference($config['id']), 'onSaveSuccess')
//                    ));
//            }
        }
    }
}

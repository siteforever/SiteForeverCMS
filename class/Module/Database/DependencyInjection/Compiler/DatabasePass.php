<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database\DependencyInjection\Compiler;

use Sfcms\Data\DataManager;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        $dispatcher = $container->getDefinition($container->getAlias('event_dispatcher'));

        /** @var DataManager $manager */
        if ($container->hasParameter('kernel.modules')) {
            $modules = $container->getParameter('kernel.modules');
            $models = array();
            /** @var string $moduleClass */
            foreach($modules as $moduleName => $moduleClass) {
                $reflectionClass = new \ReflectionClass($moduleClass);
                if (!$reflectionClass->hasMethod('config')) {
                    continue;
                }

                $config = call_user_func(array($moduleClass, 'config'));
                if (!isset($config['models'])) {
                    continue;
                }

                foreach ($config['models'] as $alias => $class) {
                    if (!preg_match('/(\w+)\\\\Model\\\\(\w+)Model$/', $class, $m)) {
                        throw new \InvalidArgumentException('Model class '. $class . ' has an inconsistent pattern');
                    }
                    $modelId = strtolower(DataManager::getModelId($moduleName, $m['2']));
                    $models[$modelId] = array(
                        'id' => $modelId,
                        'name' => $moduleName,
                        'alias' => $alias,
                        'class' => $class,
                    );
                }

//                $modelsPath = dirname($reflectionClass->getFileName()) . '/Model';
//                foreach (glob($modelsPath . '/*Model.php') as $modelFile) {
//                    if (!preg_match('/(\w+)\/Model\/(\w+)Model\.php$/', $modelFile, $m)) {
//                        throw new \InvalidArgumentException('Model file '. $modelFile . ' has an inconsistent pattern');
//                    }
//                    $modelId = strtolower(DataManager::getModelId($moduleName, $m['2']));
//                    $models[$modelId] = array(
//                        'id' => $modelId,
//                        'name' => $moduleName,
//                        'alias' => $m[2],
//                        'class' => preg_replace('/Module$/', 'Model\\' . basename($modelFile, '.php'), $moduleClass),
//                    );
//                }
            }

            $container->getDefinition('data.manager')->setArguments([
                new Reference('db'),
                new Reference('event_dispatcher'),
                $models
            ]);

            foreach ($models as $config) {
                $definition = new Definition($config['class']);
                $definition->setArguments([new Reference('data.manager')]);
                $container->setDefinition($config['id'], $definition);
                $reflectionClass = new \ReflectionClass($config['class']);
                if ($reflectionClass->implementsInterface('Symfony\Component\EventDispatcher\EventSubscriberInterface')) {
                    $dispatcher ->addMethodCall('addSubscriber', array(new Reference($config['id'])));
                }
            }
        }
    }
}

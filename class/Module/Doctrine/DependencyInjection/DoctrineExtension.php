<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Doctrine\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineExtension extends Extension
{
    /**
     * List of supported drivers and their mappings to the driver classes.
     *
     * To add your own driver use the 'driverClass' parameter to
     * {@link DriverManager::getConnection()}.
     *
     * @var array
     */
    private static $_driverMap = array(
        'pdo_mysql'  => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
        'pdo_sqlite' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
        'pdo_pgsql'  => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
        'pdo_oci' => 'Doctrine\DBAL\Driver\PDOOracle\Driver',
        'oci8' => 'Doctrine\DBAL\Driver\OCI8\Driver',
        'ibm_db2' => 'Doctrine\DBAL\Driver\IBMDB2\DB2Driver',
        'pdo_ibm' => 'Doctrine\DBAL\Driver\PDOIbm\Driver',
        'pdo_sqlsrv' => 'Doctrine\DBAL\Driver\PDOSqlsrv\Driver',
        'mysqli' => 'Doctrine\DBAL\Driver\Mysqli\Driver',
        'drizzle_pdo_mysql'  => 'Doctrine\DBAL\Driver\DrizzlePDOMySql\Driver',
        'sqlsrv' => 'Doctrine\DBAL\Driver\SQLSrv\Driver',
    );

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

        $connection = $container->getDefinition('database_connection');
        $params = $config['connection'];

        $className = self::$_driverMap[$params['driver']];
        $driver = new Definition($className);
        $container->setDefinition('doctrine.dbal.driver', $driver);

        $connection->setArguments([
                $params,
                new Reference('doctrine.dbal.driver'),
                new Reference('doctrine.configuration'),
                new Reference('doctrine.event_manager')
            ]);

        $configuration = $container->getDefinition('doctrine.configuration');

//        $container->setDefinition(
//            'doctrine.metadata.cache',
//            $this->createCacheDefinition($config['cache']['metadata'])
//        );
//        $container->setDefinition(
//            'doctrine.query.cache',
//            $this->createCacheDefinition($config['cache']['query'])
//        );
//        $container->setDefinition(
//            'doctrine.result.cache',
//            $this->createCacheDefinition($config['cache']['result'])
//        );

//        $configuration->addMethodCall('setMetadataCacheImpl', [new Reference('doctrine.metadata.cache')]);
//        $configuration->addMethodCall('setQueryCacheImpl', [new Reference('doctrine.query.cache')]);
//        $configuration->addMethodCall('setResultCacheImpl', [new Reference('doctrine.result.cache')]);
//        $configuration->addMethodCall('setProxyDir', ['%sfcms.cache_dir%/doctrine/proxy']);
//        $configuration->addMethodCall('setAutoGenerateProxyClasses', [$config['debug']]);

        foreach ($config as $key => $val) {
            $container->setParameter($this->getAlias() . '.' . $key, $val);
        }
    }

    /**
     * @param $config
     * @return Definition
     */
    private function createCacheDefinition($config)
    {
        switch ($config['type']) {
            case 'filesystem':
                return new Definition('%doctrine.cache.filesystem.class%', $config['path']);
            case 'xcache':
                return new Definition('%doctrine.cache.xcache.class%');
            case 'apc':
                return new Definition('%doctrine.cache.apc.class%');
        }
        return new Definition('%doctrine.cache.array.class%');
    }

    public function getAlias()
    {
        return 'doctrine';
    }
}

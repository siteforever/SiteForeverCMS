<?php
/**
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Database\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Sfcms\Console\Command;
use Sfcms\Data\DataManager;
use Sfcms\Data\Field;
use Sfcms\db;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Synchronization with database schema model
 *
 * Class SchemeUpdateCommand
 * @package Module\Database\Command
 */
class SchemeUpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('database:scheme:update')
            ->setDescription('Synchronization with database schema model')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forced execution queries')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');

        $container = $this->getContainer();

        /** @var Connection $conn */
        $conn = $container->get('doctrine.connection');

        $schemeManager = $conn->getSchemaManager();

        /** @var DataManager $dataManager */
        $dataManager = $container->get('data.manager');
        $currentSchema = $schemeManager->createSchema();

        $tables = [];
        if ($currentSchema->hasTable('migration')) {
            $tables[] = $currentSchema->getTable('migration');
        }
        $schema = new Schema($tables, [], $schemeManager->createSchemaConfig());

        foreach ($dataManager->getModelList() as $config) {
            $model = $dataManager->getModel($config['id']);
            $class = $model->objectClass();
            /** @var Field[] $fields */
            $fields  = call_user_func(array($class, 'fields'));
            $pk = call_user_func(array($class, 'pk'));
            $keys = call_user_func(array($class, 'keys'));
            $tableName = call_user_func(array($class, 'table'));
            $table = $schema->createTable($tableName);
            foreach ($fields as $field) {
                $column = $table->addColumn($field->getName(), Field::$types[get_class($field)], []);
                if (preg_match('/^(\d+),(\d+)$/', $field->getLength(), $m)) {
                    $column->setPrecision($m[1]);
                    $column->setScale($m[2]);
                } else {
                    $column->setLength($field->getLength());
                }
                $column->setNotnull(!$field->isNull());
                $column->setDefault($field->getDefault());
                if ($field->isAutoIncrement()) {
                    $column->setAutoincrement(true);
                }
                if ($field->getDefault()) {
                    $column->setDefault($field->getDefault());
                }
            }
            if (is_string($pk)) {
                if (false !== strpos($pk, ',')) {
                    $pk = explode(',', $pk);
                } else {
                    $pk = (array)$pk;
                }
            }
            $table->setPrimaryKey((array)$pk);
            foreach ($keys as $indexName => $columnNames) {
                $table->addIndex((array)$columnNames, $indexName);
            }
        }

        $sqlList = $currentSchema->getMigrateToSql($schema, $conn->getDatabasePlatform());

        foreach ($sqlList as $sql) {
            if (false !== strpos($sql, ' order ')) {
                $sql = str_replace(' order ', ' `order` ', $sql);
            }
            $output->writeln($sql);
            if ($force) {
                $conn->exec($sql);
            }
        }

        $output->writeln(sprintf('%s <info>%d</info> queries', $force ? 'Executed' : 'Need to execute', count($sqlList)));
    }
}

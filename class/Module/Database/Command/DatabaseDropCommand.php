<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database\Command;


use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Sfcms\Console\Command;
use Sfcms\db;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDropCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('database:drop')
            ->setDescription('Dropping database like config')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $this->getContainer()->getParameter('doctrine.connection');
        $dbName = $params['dbname'];
        unset($params['dbname']);
        $connection = DriverManager::getConnection($params);

        $sm = $connection->getSchemaManager();
        $databases = $sm->listDatabases();
        if (!in_array($dbName, $databases)) {
            $output->writeln(sprintf('<error>Database "%s" is not exists</error>', $dbName));
            return;
        }
        try {
            $sm->dropDatabase($dbName);
        } catch (DriverException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return;
        }

        $output->writeln(sprintf('<info>Database "%s" dropped successfully</info>', $dbName));
    }
}

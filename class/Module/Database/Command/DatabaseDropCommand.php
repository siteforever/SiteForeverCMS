<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Database\Command;


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
        /** @var db $db */
        $db = $this->getContainer()->get('db');
        $dbConfig = $this->getContainer()->getParameter('database');
        $dbName = $dbConfig['name'];
        $db->dropDatabase($dbName);
        $output->writeln(sprintf('<info>Database "%s" dropped successfully</info>', $dbName));
    }
}

<?php
/**
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Database\Command;

use Sfcms\Console\Command;
use Sfcms\Data\DataManager;
use Sfcms\Data\SchemeManager;
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

        $kernel = $this->getApplication()->getKernel();
        $container = $kernel->getContainer();
        /** @var DataManager $dataManager */
        $dataManager = $container->get('data.manager');
        /** @var SchemeManager $schemeManager */
        $schemeManager = $container->get('data.scheme.manager');
        /** @var db $db */
        $db = $container->get('db');

        $count = 0;
        foreach ($dataManager->getModelList() as $config) {
            foreach ($schemeManager->migrate($dataManager->getModel($config['id'])) as $query) {
                $count++;
                if ($force) {
                    $db->query($query);
                } else {
                    $output->writeln(sprintf('<comment>%s</comment>', $query));
                }
            }
        }

        $output->writeln(sprintf('%s <info>%d</info> queries', $force ? 'Executed' : 'Need to execute', $count));
    }
}

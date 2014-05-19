<?php
/**
 * Консоль
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms;

use Sfcms\Kernel\AbstractKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleApplication extends Application
{
    private $app;

    public function __construct(AbstractKernel $kernel)
    {
        $this->app = $kernel;

        parent::__construct('SfCms', '0.7'.' - '.$kernel->getEnvironment().($kernel->isDebug() ? '/debug' : ''));

        $this->getDefinition()->addOption(new InputOption('--shell', '-s', InputOption::VALUE_NONE, 'Launch the shell.'));
        $this->getDefinition()->addOption(new InputOption('--process-isolation', null, InputOption::VALUE_NONE, 'Launch commands from shell as a separate process.'));
        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $kernel->getEnvironment()));
        $this->getDefinition()->addOption(new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'));
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->setDispatcher($this->app->getEventDispatcher());
        $this->registerCommands();
        return parent::doRun($input, $output);
    }

    protected function registerCommands()
    {
        foreach ($this->app->getModules() as $module) {
            if ($module instanceof Module) {
                $module->registerCommands($this);
            }
        }

//        $this->addCommands(array(
//            // DBAL Commands
//            new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
//            new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),
//
//            // ORM Commands
//            new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand(),
//            new \Doctrine\ORM\Tools\Console\Command\InfoCommand()
//        ));
//
//        $this->addCommands([
//                new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
//                new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
//                new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
//                new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
//                new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
//                new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
//            ]);
    }

    public function getKernel()
    {
        return $this->app;
    }
}

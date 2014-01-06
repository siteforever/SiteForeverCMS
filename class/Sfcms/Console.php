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

class Console extends Application
{
    private $app;

    public function __construct(AbstractKernel $kernel)
    {
        $this->app = $kernel;

        parent::__construct('SfCms', '0.6'.' - '.$kernel->getEnvironment().($kernel->isDebug() ? '/debug' : ''));

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
    }

    public function getKernel()
    {
        return $this->app;
    }
}

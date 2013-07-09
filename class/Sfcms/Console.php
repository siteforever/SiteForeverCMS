<?php
/**
 * Консоль
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms;


use Sfcms\Kernel\AbstractKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Console extends Application
{
    private $app;

    public function __construct(AbstractKernel $app)
    {
        parent::__construct('sfcms');
        $this->app = $app;
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

}

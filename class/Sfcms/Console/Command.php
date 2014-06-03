<?php
/**
 * Subclass for symfony command class
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\DependencyInjection\Container;

class Command extends SymfonyCommand
{
    /**
     * @return \Sfcms\ConsoleApplication
     */
    public function getApplication()
    {
        return parent::getApplication();
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->getApplication()->getKernel()->getContainer();
    }
}

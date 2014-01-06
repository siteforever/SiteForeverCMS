<?php
/**
 * Subclass for symfony command class
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
    /**
     * @return \Sfcms\Console
     */
    public function getApplication()
    {
        return parent::getApplication();
    }

    public function getContainer()
    {
        return $this->getApplication()->getKernel()->getContainer();
    }
}

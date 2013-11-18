<?php
/**
 * Command for debugging container
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContainerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('debug:container')
            ->setDescription('Print all services')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $continer = \App::cms()->getContainer();
        $services = $continer->getServiceIds();
        foreach ($services as $sid) {
            $output->writeln(sprintf('%s: <info>%s</info>', $sid, $continer->hasDefinition($sid) ? $continer->getDefinition($sid)->getClass() : ''));
        }
    }
}

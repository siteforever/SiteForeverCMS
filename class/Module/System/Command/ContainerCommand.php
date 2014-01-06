<?php
/**
 * Command for debugging container
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\System\Command;

use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContainerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('container:debug')
            ->setDescription('Print all services')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $continer = $this->getApplication()->getKernel()->createNewContainer();
        $services = $continer->getServiceIds();
        sort($services);

        $len = array_reduce($services, function($result, $val){
                return $result < strlen($val) ? strlen($val) : $result;
            }, 0) + 5;

        foreach ($services as $sid) {
            $output->writeln(
                sprintf('%\' -' . $len . 's: <info>%s</info>',
                    $sid,
                    $continer->hasDefinition($sid)
                        ? ($continer->getDefinition($sid)->isSynthetic()
                            ? "?synthetic"
                            : $continer->getDefinition($sid)->getClass()
                        )
                        : ($continer->hasAlias($sid)
                            ? sprintf('<comment>alias for:</comment> %s', $continer->getAlias($sid))
                            : '-'
                        )
                )
            );
        }
    }
}

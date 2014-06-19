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
        $container = $this->getApplication()->getKernel()->createNewContainer();
        $container->compile();
        $services = $container->getServiceIds();
        sort($services);

        $len = array_reduce($services, function($result, $val){
                return $result < strlen($val) ? strlen($val) : $result;
            }, 0) + 5;

        foreach ($services as $sid) {
            $output->writeln(
                sprintf('%\' -' . $len . 's: <info>%s</info>',
                    $sid,
                    $container->hasDefinition($sid)
                        ? ($container->getDefinition($sid)->isSynthetic()
                            ? "<comment>synthetic</comment>"
                            : $container->getDefinition($sid)->getClass()
                        )
                        : ($container->hasAlias($sid)
                            ? sprintf('<comment>alias for:</comment> %s', $container->getAlias($sid))
                            : '-'
                        )
                )
            );
        }

        foreach($container->getParameterBag()->all() as $key => $val) {
            $output->writeln(sprintf('<info>%s</info> %s', $key, is_scalar($val)
                ? $val
                : (is_null($val)
                    ? 'null'
                    : (is_array($val) ? 'array('. count($val) . ')' : '[value]')
                  )
                )
            );
        }
    }
}

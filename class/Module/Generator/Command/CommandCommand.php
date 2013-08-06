<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @link http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
 */

namespace Module\Generator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generator:command')
            ->setDescription('Generate new command class')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'What is name of module?')
            ->addArgument('commandName', InputArgument::REQUIRED, 'What is name of command?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modules = include(getcwd() . '/app/modules.php');
        $moduleName = ucfirst(strtolower($input->getArgument('moduleName')));
        $commandName = ucfirst(strtolower($input->getArgument('commandName')));
        $ns = 'Module\\'.$moduleName;

        $exists = array_reduce($modules, function($exists, $module) use ($moduleName, $output) {
                return $exists |= $module['name'] == $moduleName ? true : false;
            }, false);

        if (!$exists) {
            $output->writeln(sprintf("<error>Module '%s' not exists</error>", $moduleName));
            return;
        }

        $commandClass = $ns . '\\Command\\' . $commandName . 'Command';
        if (class_exists($commandClass)) {
            $output->writeln(sprintf("<error>Command '%s' already exists exists</error>", $commandClass));
            return;
        }

        $output->writeln(sprintf("<info>Generating new class '%s'</info>", $commandClass));
        $commandFile = $modulePath = getcwd() . '/class/'. str_replace('\\', '/', $commandClass) . '.php';
        $output->writeln(sprintf("<info>New file '%s'</info>", $commandFile));
        if (!is_dir(dirname($commandFile))) {
            mkdir(dirname($commandFile), 0755, true);
            $output->writeln(sprintf("<info>Created directory '%s'</info>", dirname($commandFile)));
        }
        $tpl = \App::cms()->getTpl();
        $tpl->assign(array(
                'commandName' => $commandName,
                'moduleName' => $moduleName,
                'ns' => $ns,
            ));
        file_put_contents($commandFile, $tpl->fetch('generator.command'));
    }

}

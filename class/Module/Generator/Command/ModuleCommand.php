<?php
/**
 * Modules generator
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Generator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generator:module')
            ->setDescription('Generate new module structure')
            ->addArgument('name', InputArgument::REQUIRED, 'What is name of module?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modulesConfigFile = getcwd() . '/app/modules.php';
        $modules = include($modulesConfigFile);
        $name = ucfirst(strtolower($input->getArgument('name')));
        $ns = 'Module\\'.$name;
        $exists = array_reduce($modules, function($exists, $module) use ($name, $output) {
                return $exists |= $module['name'] == $name ? true : false;
            }, false);

        if ($exists) {
            $output->writeln(sprintf("<error>Module %s already exists</error>", $name));
            return;
        }
        $modulePath = getcwd() . '/class/Module/'.$name;
        $output->writeln(sprintf('<info>Path to module %s: %s</info>', $name, $modulePath));
        if (is_dir($modulePath)) {
            $output->writeln(sprintf("<error>Module path already exists</error>"));
            return;
        }
        mkdir($modulePath, 0755, true);
        mkdir($modulePath.'/Command', 0755, true);
        mkdir($modulePath.'/Controller', 0755, true);
        mkdir($modulePath.'/Model', 0755, true);
        mkdir($modulePath.'/Object', 0755, true);
        mkdir($modulePath.'/Test', 0755, true);
        mkdir($modulePath.'/View', 0755, true);
        $tpl = \App::cms()->getTpl();
        $tpl->assign(array(
                'name' => $name,
                'ns' => $ns,
            ));
        $moduleContent = $tpl->fetch('generator.module');
        file_put_contents($modulePath . '/Module.php', $moduleContent);
        $output->writeln(sprintf('<info>Created module %s</info>', $name));

        $modules[] = array('name'=>$name, 'path'=>$ns);
        $tpl->assign('modules', $modules);
        $modulesList = $tpl->fetch('generator.modules_list');
        file_put_contents($modulesConfigFile, $modulesList);
        $output->writeln(sprintf('<info>Updated modules list</info>'));

    }

}

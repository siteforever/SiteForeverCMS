<?php
/**
 * Modules generator
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Generator\Command;

use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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

        $fs = new Filesystem();

        $modulePath = getcwd() . '/class/Module/'.$name;
        $output->writeln(sprintf('<info>Path to module %s: %s</info>', $name, $modulePath));
        if ($fs->is_dir($modulePath)) {
            $output->writeln(sprintf("<error>Module path already exists</error>"));
            return;
        }
        $fs->mkdir($modulePath, 0755, true);
        $fs->mkdir($modulePath.'/Command', 0755, true);
        $fs->mkdir($modulePath.'/Controller', 0755, true);
        $fs->mkdir($modulePath.'/Model', 0755, true);
        $fs->mkdir($modulePath.'/Object', 0755, true);
        $fs->mkdir($modulePath.'/Test', 0755, true);
        $fs->mkdir($modulePath.'/View', 0755, true);
        $tpl = $this->getApplication()->getKernel()->getTpl();
        $tpl->assign(array(
                'name' => $name,
                'ns' => $ns,
            ));
        $moduleContent = $tpl->fetch('generator.module');
        $fs->dumpFile($modulePath . '/Module.php', $moduleContent, 0644);
        $output->writeln(sprintf('<info>Created module %s</info>', $name));



        $modules[] = array('name'=>$name, 'path'=>$ns);
        $tpl->assign('modules', $modules);
        $modulesList = $tpl->fetch('generator.modules_list');
        $fs->dumpFile($modulesConfigFile, $modulesList, 0644);
        $output->writeln(sprintf('<info>Updated modules list</info>'));
    }

}

<?php
namespace Module\Install\Command;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\AssetWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Install all static resource
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
class StaticCommand extends Command
{
    protected function configure()
    {
        $this->setName('install:static')
            ->setDescription('Installing all static files for vendors')
            ->addArgument('dir', InputArgument::OPTIONAL, 'In which directory to install?', 'static')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $staticDir = ROOT . '/' . trim($input->getArgument('dir'), '\\/ '.PHP_EOL);

        $output->writeln(sprintf('<info>Static dir is: "%s"</info>', $staticDir));

        $this->installJqGrid($staticDir, $input, $output);
        $this->installElFinder($staticDir, $input, $output);

        $output->writeln('<info>Command Install</info>');

        file_put_contents(ROOT . '/vendor/.htaccess', "deny from all");
    }



    protected function installJqGrid($staticDir, InputInterface $input, OutputInterface $output)
    {
        $source = ROOT . '/vendor/tonytomov';
        $out    = $staticDir . '/admin/jquery/jqgrid';

        $modules = array(
            new FileAsset($source . '/jqGrid/js/i18n/grid.locale-ru.js'),
            new FileAsset($source . '/jqGrid/js/grid.base.js'),
            new FileAsset($source . '/jqGrid/js/grid.common.js'),
            new FileAsset($source . '/jqGrid/js/grid.formedit.js'),
            new FileAsset($source . '/jqGrid/js/grid.inlinedit.js'),
            new FileAsset($source . '/jqGrid/js/grid.celledit.js'),
            new FileAsset($source . '/jqGrid/js/grid.subgrid.js'),
            new FileAsset($source . '/jqGrid/js/grid.treegrid.js'),
            new FileAsset($source . '/jqGrid/js/grid.grouping.js'),
            new FileAsset($source . '/jqGrid/js/grid.custom.js'),
            new FileAsset($source . '/jqGrid/js/grid.tbltogrid.js'),
            new FileAsset($source . '/jqGrid/js/grid.import.js'),
            new FileAsset($source . '/jqGrid/js/jquery.fmatter.js'),
            new FileAsset($source . '/jqGrid/js/JsonXml.js'),
            new FileAsset($source . '/jqGrid/js/grid.jqueryui.js'),
            new FileAsset($source . '/jqGrid/js/grid.filter.js'),
        );
        $jsAsset = new AssetCollection($modules, array(), $source);
        $jsAsset->setTargetPath('jqgrid.js');
        $writer = new AssetWriter($out);
        $writer->writeAsset($jsAsset);

        $cssAsset = new FileAsset($source . '/jqGrid/css/ui.jqgrid.css');
        $cssAsset->setTargetPath('ui.jqgrid.css');
        $writer->writeAsset($cssAsset);

        $output->writeln(sprintf('<info>Js "%s" was updated.</info>', $jsAsset->getTargetPath()));
        $output->writeln(sprintf('<info>Css "%s" was updated</info>', $cssAsset->getTargetPath()));
    }

    /**
     * @param                 $staticDir
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function installElFinder($staticDir, InputInterface $input, OutputInterface $output)
    {
        $source = ROOT . '/vendor/helios-ag/fm-elfinder/FM/elfinder';
        $out    = $staticDir . '/admin/jquery/elfinder';

        $jsAsset = new AssetCollection(array(
            new FileAsset($source . '/js/elFinder.js'),
            new FileAsset($source . '/js/elFinder.version.js'),
            new FileAsset($source . '/js/jquery.elfinder.js'),
            new FileAsset($source . '/js/elFinder.resources.js'),
            new FileAsset($source . '/js/elFinder.options.js'),
            new FileAsset($source . '/js/elFinder.history.js'),
            new FileAsset($source . '/js/elFinder.command.js'),
            new GlobAsset($source . '/js/ui/*.js'),
            new GlobAsset($source . '/js/commands/*.js'),
            new FileAsset($source . '/js/jquery.dialogelfinder.js'),
            new GlobAsset($source . '/js/proxy/*.js'),
            new GlobAsset($source . '/js/i18n/*.ru.js'),
            new GlobAsset($source . '/js/i18n/*.en.js'),
        ));

        $jsAsset->setTargetPath('elfinder.js');

        $cssAsset = new GlobAsset($source . '/css/*.css');
        $cssAsset->setTargetPath('elfinder.css');

        $writer = new AssetWriter($out);
        $writer->writeAsset($jsAsset);
        $writer->writeAsset($cssAsset);

        $output->writeln(sprintf('<info>ElFinder js to "%s"</info>', $jsAsset->getTargetPath()));
        $output->writeln(sprintf('<info>ElFinder css to "%s"</info>', $cssAsset->getTargetPath()));

        $filesistem = new Filesystem();
        $filesistem->symlink($source . '/img', $out . '/../img');
        $output->writeln(sprintf('<info>ElFinder img symlink to "%s"</info>', $out . '/../img'));
    }

}

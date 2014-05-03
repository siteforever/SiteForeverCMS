<?php
namespace Module\Install\Command;

use Assetic\AssetWriter;
use Module\Install\Event\StaticEvent;
use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Install all static resource
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
class StaticCommand extends Command
{
    /** @var ContainerBuilder */
    protected $container;

    protected function configure()
    {
        $this->setName('install:static')
            ->setDescription('Installing all static files for vendors')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerBuilder */
        $this->container = $this->getContainer();

        $staticDir = $this->getContainer()->getParameter('assetic.output') . '/static';
        $sfPath = $this->getContainer()->getParameter('sf_path');
        $rootDir = $this->getContainer()->getParameter('root');

        $output->writeln('<info>Command Install</info>');
        $output->writeln(sprintf('<info>Static dir is: "%s"</info>', $staticDir));

        /** @var EventDispatcher $ed */
        $ed = $this->getContainer()->get('event.dispatcher');
        $event = new StaticEvent($staticDir, $input, $output);
        $ed->addListener(StaticEvent::STATIC_INSTALL, array($this, 'installRequireJs'));
        $ed->dispatch(StaticEvent::STATIC_INSTALL, $event);

        $filesistem = new Filesystem();

        if (!$filesistem->exists($staticDir . '/images')) {
            $filesistem->mirror(
                $this->getContainer()->getParameter('sf_path') . '/class/Module/System/Static/images',
                $staticDir . '/images',
                null,
                array('override'=>true)
            );
        }

        if ($rootDir != $sfPath && !$filesistem->exists($rootDir . '/misc')) {
            $filesistem->symlink($sfPath . '/misc', $rootDir . '/misc');
            $output->writeln('<info>Create symlink for "misc"</info>');
        }
        if (!$filesistem->exists($rootDir . '/files')) {
            $filesistem->mkdir($rootDir . '/files', 0777);
            $output->writeln('<info>Create "files" dir</info>');
        }
        if (!$filesistem->exists($rootDir . '/runtime')) {
            $filesistem->mkdir(array($rootDir . '/runtime/cache', $rootDir . '/runtime/templates_c', $rootDir . '/runtime/logs',));
            $output->writeln('<info>Create "runtime" dir</info>');
        }

        if (!$filesistem->exists($rootDir . '/vendor/.htaccess')) {
            $filesistem->dumpFile($rootDir . '/vendor/.htaccess', "deny from all", 0644);
            $output->writeln('<info>Create "vendor/.htaccess" file</info>');
        }

        $template = $this->container->getParameter('template');
        $themePath = $rootDir . '/themes/' . $template['theme'];
        if (!$filesistem->exists($themePath)) {
            $filesistem->mkdir($themePath);
            $filesistem->mirror($sfPath.'/themes/basic', $themePath);
            $output->writeln(sprintf('Create theme dir "%s"', $themePath));
        }
    }

    public function installRequireJs(StaticEvent $event)
    {
        $asset = $this->container->get('asset.service')->getAsseticCollection('require_js');
        $writer = new AssetWriter($event->getStaticDir());
        $writer->writeAsset($asset);

        $event->getOutput()->writeln(sprintf('<info>Js "%s" was updated.</info>', $asset->getTargetPath()));
    }
}

<?php
namespace Module\System\Command;

use Assetic\Asset\AssetCollection;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Module\System\Event\StaticEvent;
use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Install all static resource
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
class StaticCommand extends Command
{
    /** @var Container */
    protected $container;

    protected function configure()
    {
        $this->setName('system:static')
            ->setDescription('Installing all static files for vendors')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerBuilder */
        $this->container = $this->getContainer();
        $staticDir = $this->getContainer()->getParameter('assetic.output') . '/static';
        $rootDir = $this->getContainer()->getParameter('root');

        $output->writeln('<info>Command Install</info>');
        $output->writeln(sprintf('<info>Static dir is: "%s"</info>', $staticDir));

        /** @var EventDispatcher $ed */
        $ed = $this->getContainer()->get('event.dispatcher');
        $event = new StaticEvent($staticDir, $input, $output);
        $ed->dispatch(StaticEvent::STATIC_INSTALL, $event);

        $filesistem = new Filesystem();

        if (!$filesistem->exists($staticDir . '/images')) {
            $filesistem->mirror(
                $this->getContainer()->getParameter('sf_path') . '/class/Module/System/static/images',
                $staticDir . '/images',
                null,
                array('override'=>true)
            );
        }

        if (!$filesistem->exists($rootDir . '/files')) {
            $filesistem->mkdir($rootDir . '/files', 0777);
            $output->writeln('<info>Create "files" dir</info>');
        }
        if (!$filesistem->exists($rootDir . '/runtime')) {
            $filesistem->mkdir(array($rootDir . '/runtime/cache', $rootDir . '/runtime/templates', $rootDir . '/runtime/logs',));
            $output->writeln('<info>Create "runtime" dir</info>');
        }

        if (!$filesistem->exists($rootDir . '/vendor/.htaccess')) {
            $filesistem->dumpFile($rootDir . '/vendor/.htaccess', "deny from all", 0644);
            $output->writeln('<info>Create "vendor/.htaccess" file</info>');
        }

//        $template = $this->container->getParameter('template');
//        $themePath = $rootDir . '/themes/' . $template['theme'];
//        if (!$filesistem->exists($themePath)) {
//            $filesistem->mkdir($themePath);
//            $filesistem->mirror($sfDir.'/themes/basic', $themePath);
//            $output->writeln(sprintf('Create theme dir "%s"', $themePath));
//        }
    }
}

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
        $staticDir = ROOT . '/static';
        $rootDir = $this->getContainer()->getParameter('root');
        $cacheDir = $this->getContainer()->getParameter('sfcms.cache_dir');
        $logsDir = $this->getContainer()->getParameter('sfcms.log_dir');

        $output->writeln('<info>Command Install</info>');
        $output->writeln(sprintf('<info>Static dir is: "%s"</info>', $staticDir));

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->getContainer()->get('event.dispatcher');
        $event = new StaticEvent($staticDir, $input, $output);
        $eventDispatcher->dispatch(StaticEvent::STATIC_INSTALL, $event);

        $filesistem = new Filesystem();

        if (!$filesistem->exists($staticDir . '/images')) {
            $filesistem->mirror(
                $this->getContainer()->getParameter('sfcms.path') . '/class/Module/System/static/images',
                $staticDir . '/images',
                null,
                array('override'=>true)
            );
        }

        if (!$filesistem->exists($rootDir . '/files')) {
            $filesistem->mkdir($rootDir . '/files', 0777);
            $output->writeln('<info>Create "files" dir</info>');
        }
        if (!$filesistem->exists($cacheDir)) {
            $filesistem->mkdir([$cacheDir, $cacheDir . '/templates', $logsDir]);
            $output->writeln('<info>Create "runtime" dir</info>');
        }

        if (!$filesistem->exists($rootDir . '/vendor/.htaccess')) {
            $filesistem->dumpFile($rootDir . '/vendor/.htaccess', "deny from all", 0644);
            $output->writeln('<info>Create "vendor/.htaccess" file</info>');
        }

        if (ROOT != SF_PATH) {
            $files = [
                '.bowerrc',
                'bower.json',
                'package.json',
                'Gruntfile.js',
            ];
            foreach ($files as $file) {
                if (!$filesistem->exists(ROOT . '/' . $file)) {
                    $filesistem->copy(SF_PATH . '/' . $file, ROOT . '/' . $file);
                }
            }
        }
    }
}

<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Elfinder\Listener;


use Assetic\AssetWriter;
use Module\System\Event\StaticEvent;
use Module\System\Service\AsseticService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class StaticListener
{
    /** @var AsseticService */
    private $asseticService;

    /** @var string */
    private $rootDir;

    public function __construct(AsseticService $asseticService, $rootDir)
    {
        $this->asseticService = $asseticService;
        $this->rootDir = $rootDir;
    }

    /**
     * @param $event
     */
    public function installElFinder(StaticEvent $event)
    {
        $source = $this->rootDir . '/vendor/studio-42/elfinder';
        $out    = $event->getStaticDir() . '/admin/jquery/elfinder';

        $jsAsset = $this->asseticService->getAsseticCollection('elfinder_js');
        $cssAsset = $this->asseticService->getAsseticCollection('elfinder_css');

        $writer = new AssetWriter($out);
        $writer->writeAsset($jsAsset);
        $writer->writeAsset($cssAsset);

        $event->getOutput()->writeln(sprintf('<info>ElFinder js to "%s"</info>', $jsAsset->getTargetPath()));
        $event->getOutput()->writeln(sprintf('<info>ElFinder css to "%s"</info>', $cssAsset->getTargetPath()));

        $filesistem = new Filesystem();
        $filesistem->symlink($source . '/img', $out . '/../img');
        $event->getOutput()->writeln(sprintf('<info>ElFinder img symlink to "%s"</info>', $out . '/../img'));
    }
}

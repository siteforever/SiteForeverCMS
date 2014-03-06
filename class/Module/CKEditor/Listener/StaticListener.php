<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\CKEditor\Listener;


use Assetic\AssetWriter;
use Module\Install\Event\StaticEvent;
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
    public function installCKEditor(StaticEvent $event)
    {
        $fs = new Filesystem();
        $source = $this->rootDir . '/vendor/ckeditor/ckeditor';
        $target = $event->getStaticDir() . '/ckeditor';
        if ($fs->exists($target)) {
            $fs->remove($target);
        }
        $fs->symlink($source, $target);
        $event->getOutput()->writeln('<info>Installing <comment>CKEditor</comment> complete</info>');
    }
}

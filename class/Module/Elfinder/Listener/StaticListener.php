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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class StaticListener
{
    /** @var string */
    private $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param $event
     */
    public function installElFinder(StaticEvent $event)
    {
        $source = $this->rootDir . '/vendor/studio-42/elfinder';
        $out    = $event->getStaticDir() . '/admin/jquery/elfinder';

        $filesistem = new Filesystem();
        $filesistem->remove($out . '/../img');
        $filesistem->symlink($source . '/img', $out . '/../img');
        $filesistem->copy($source . '/css/elfinder.min.css', $out . '/elfinder.css', true);
        $filesistem->copy($source . '/css/theme.css', $out . '/theme.css', true);
        $filesistem->copy($source . '/js/i18n/elfinder.ru.js', $out . '/i18n/elfinder.ru.js', true);
        $filesistem->copy($source . '/js/elfinder.full.js', $out . '/elfinder.js', true);
        $filesistem->copy($source . '/js/elfinder.min.js', $out . '/elfinder.min.js', true);

        $event->getOutput()->writeln(sprintf('<info>ElFinder img symlink to "%s"</info>', $out . '/../img'));
    }
}

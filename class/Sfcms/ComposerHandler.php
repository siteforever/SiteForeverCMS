<?php
namespace Sfcms;

use Composer\Script\Event;
use Sfcms\Composer\AbstractHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Delegates the call to the installation of static files
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
class ComposerHandler extends AbstractHandler
{
    public static function install(Event $event)
    {
        $cwd = getcwd();
        $cms = realpath(__DIR__ . '/../..');
        $fs = new Filesystem();
        if ($cms != $cwd) {
            if (!$fs->exists($cwd)) {
                $fs->mkdir($cwd, 0755);
            }
            if (!$fs->exists($cwd . '/app/.htaccess')) {
                $fs->copy($cms . '/app/.htaccess', $cwd . '/app/.htaccess');
            }
            if (!$fs->exists($cwd . '/.htaccess')) {
                $fs->copy($cms . '/.htaccess', $cwd . '/.htaccess');
            }
            if (!$fs->exists($cwd . '/index.php')) {
                $fs->copy($cms . '/index.php', $cwd . '/index.php');
            }
        }
    }

    public static function execute(Event $event)
    {
        static::executeCommand($event, 'system:static');
        static::executeCommand($event, 'translator:generate');
    }
}

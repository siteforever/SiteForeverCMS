<?php
namespace Sfcms;

use Composer\Script\Event;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Delegates the call to the installation of static files
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
class ComposerHandler
{
    public static function install(Event $event)
    {
        $cwd = getcwd();
        $cms = realpath(__DIR__ . '/../..');
        if ($cms != $cwd) {
            if (!is_dir($cwd)) {
                @mkdir($cwd, 0755, true);
            }
            if (!is_file($cwd . '/app/console')) {
                copy($cms . '/app/console', $cwd . '/app/console');
                chmod($cwd . '/app/console', 0755);
            }
            if (!is_file($cwd . '/app/.htaccess')) {
                copy($cms . '/app/.htaccess', $cwd . '/app/.htaccess');
            }
            if (!is_file($cwd . '/.htaccess')) {
                copy($cms . '/.htaccess', $cwd . '/.htaccess');
            }
            if (!is_file($cwd . '/index.php')) {
                copy($cms . '/index.php', $cwd . '/index.php');
            }
        }
    }

    public static function execute(Event $event)
    {
        $options = self::getOptions($event);
        $appDir = $options['sfcms-app-dir'];

//        static::executeCommand($event, $appDir, 'database:scheme:update --force');
        static::executeCommand($event, $appDir, 'install:static');
        static::executeCommand($event, $appDir, 'translator:generate');
    }

    protected static function executeCommand(Event $event, $appDir, $cmd, $timeout = 300)
    {
        $php = escapeshellarg(self::getPhp());
        $console = escapeshellarg($appDir.'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) { echo $buffer; });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd)));
        }
    }

    protected static function getOptions(Event $event)
    {
        $options = array_merge(array(
                'sfcms-app-dir' => 'app',
                'sfcms-static-dir' => 'static'
            ), $event->getComposer()->getPackage()->getExtra());

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }
}

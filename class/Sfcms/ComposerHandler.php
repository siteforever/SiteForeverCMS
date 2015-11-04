<?php
namespace Sfcms;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Composer\Script\CommandEvent;

/**
 * Delegates the call to the installation of static files
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
class ComposerHandler
{
    public static function execute(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $staticDir = $options['sfcms-static-dir'];
        $appDir = $options['sfcms-app-dir'];

        mkdir($appDir . '/runtime/cache', 0777, true);
        mkdir($appDir . '/runtime/logs', 0777, true);

        static::executeCommand($event, $appDir, 'install:static ' . $staticDir);
    }

    protected static function executeCommand(CommandEvent $event, $appDir, $cmd, $timeout = 300)
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

    protected static function getOptions(CommandEvent $event)
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

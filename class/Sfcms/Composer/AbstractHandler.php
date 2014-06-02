<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Sfcms\Composer;

use Composer\Script\Event;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

abstract class AbstractHandler
{
    protected static function executeCommand(Event $event, $cmd, $timeout = 300)
    {
        $cwd = getcwd();
        $appDir = ["app", "bin"];
        $locator = new FileLocator($appDir);
        $console = escapeshellarg($locator->locate('console', $cwd));

        $php = escapeshellarg(self::getPhp());
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

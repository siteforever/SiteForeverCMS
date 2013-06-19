<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Assetic;


use Symfony\Component\Process\Process;

class ComposerHandler
{
    public static function installAdmin()
    {
        print getcwd() . '/console page:assets'.PHP_EOL;
        $process = new Process(getcwd() . '/console page:assets');
        $process->run(function($type, $buffer){
                print $buffer;
            });
    }
}



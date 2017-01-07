<?php

use Codeception\Configuration as Config;
use Codeception\Events;
use Symfony\Component\Process\Process;

class EventObserverExtension extends \Codeception\Platform\Extension
{
    public static $runnedBefore = false;
    public static $runnedAfter = false;

    public static $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
        Events::SUITE_AFTER => 'afterSuite',
        Events::TEST_BEFORE => 'beforeTest',
        Events::TEST_AFTER => 'afterTest',
    ];

    protected function run($cmd, $async = false)
    {
        $cmd = str_replace('{env}', 'test', $cmd);
        $this->writeln($cmd);
        $process = new Process($cmd);
        $process->run();
        $this->writeln($process->getOutput());
        $this->writeln('<info>Exit code: ' . $process->getExitCode() . ' ' . $process->getExitCodeText() . '</info>');
        if (!$process->isSuccessful()) {
            $this->writeln('<error>' . $process->getErrorOutput() . '</error>');
            die();
        }
        $this->writeln(str_repeat('-', 80) . PHP_EOL);

        return false;
    }


    public function beforeSuite(Codeception\Event\SuiteEvent $e)
    {
        if (!static::$runnedBefore) {
            $this->run('rm -rf var/cache/{env}');
            $this->run('php bin/console --env={env} doctrine:database:drop --if-exists --force');
            $this->run('php bin/console --env={env} doctrine:database:create --if-not-exists -n');
            $this->run('php bin/console --env={env} database:scheme:update --force');
            $this->run('php bin/console --env={env} fixture:users');
            $this->run('php bin/console --env={env} fixture:pages');
            static::$runnedBefore = true;
        }
    }

    public function afterSuite(Codeception\Event\SuiteEvent $e)
    {
        if (!static::$runnedAfter) {
            static::$runnedAfter = true;
        }
    }

    public function beforeTest(Codeception\Event\TestEvent $e)
    {

    }

    public function afterTest(Codeception\Event\TestEvent $e)
    {

    }
}

<?php
/**
 * Запуск тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
//корень сайта
defined('ROOT') || define('ROOT', realpath(__DIR__ . '/..'));

require_once 'vendor/autoload.php';

use Behat\Mink\Driver\ZombieDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\NodeJS\Server\ZombieServer;
use Symfony\Component\Process\Process;

//$mysqlFrom = "mysqldump -u root --add-drop-database=TRUE siteforever";
$mysqlTo = "mysql -u root siteforever_test";

$process = new Process("$mysqlTo < ".__DIR__."/dump.sql");
print $process->getCommandLine() . PHP_EOL;
$process->start();
while ($process->isRunning()) {
    print "database restoring...\n";
    sleep(1);
}
if (!$process->isSuccessful()) {
    print_r($process->getErrorOutput());
    exit(255);
}

$process = new Process("php app/console --env=test database:scheme:update --force");
print $process->getCommandLine() . PHP_EOL;
$process->start();
while ($process->isRunning()) {
    sleep(1);
}
if (!$process->isSuccessful()) {
    print_r($process->getErrorOutput());
    exit(255);
}
print $process->getOutput();

$app = new App('test', true);

var_dump($_ENV, $_SERVER);

$capabilities = [];
$hubUri = 'http://localhost:4444/wd/hub';
if (defined('TRAVIS')) {
    $capabilities['tunnel-identifier'] = $_ENV['TRAVIS_JOB_NUMBER'];
    $capabilities['build'] = $_ENV['TRAVIS_BUILD_NUMBER'];
    $capabilities['tags'] = [$_ENV['TRAVIS_PYTHON_VERSION'], 'CI'];
    $hubUri = sprintf('http://%s:%s@localhost:4445/wd/hub', $_ENV['SAUCE_USERNAME'], $_ENV['SAUCE_ACCESS_KEY']);
}
$startUrl = 'http://test.cms.sf';
$mink = new Mink([
    'zombie' => new Session(new ZombieDriver(new ZombieServer())),
    'selenium' => new Session(new Selenium2Driver('firefox', $capabilities, $hubUri)),
    'goutte' => new Session(new GoutteDriver),
]);
$mink->setDefaultSessionName('selenium');
$mink->getSession()->setCookie('XDEBUG_SESSION', 'PHPSTORM');

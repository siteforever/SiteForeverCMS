<?php
/**
 * Запуск тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
//корень сайта
defined('ROOT') || define('ROOT', realpath(__DIR__ . '/..'));

require_once 'vendor/autoload.php';

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Symfony\Component\Process\Process;

//$capabilities = Selenium2Driver::getDefaultCapabilities();
//$capabilities['selenium-version'] = '2.41.0';
//$hubUri = 'http://localhost:4444/wd/hub';
$host = 'localhost';
$port = '1888';
$startUrl = sprintf('http://%s:%s', $host, $port);
//$travis = false;
//if (!empty($_SERVER['TRAVIS'])) {
//    $travis = true;
//    $capabilities['tunnel-identifier'] = $_SERVER['TRAVIS_JOB_ID'];
//    $capabilities['build'] = $_SERVER['TRAVIS_BUILD_NUMBER'];
//    $capabilities['tags'] = [$_SERVER['TRAVIS_PHP_VERSION'], 'CI'];
//    $hubUri = sprintf('http://%s:%s@localhost:4445/wd/hub', $_SERVER['SAUCE_USERNAME'], $_SERVER['SAUCE_ACCESS_KEY']);
//    $startUrl = sprintf('http://%s:%s', $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT']);
//} else {

// Command that starts the built-in web server
$command = sprintf('php -S %s:%d -t %s >/dev/null 2>&1 & echo $!', $host, $port, realpath(__DIR__ . '/..'));
// Execute the command and store the process ID
$output = array();
exec($command, $output);
sleep(1);
$pid = (int) $output[0];

echo sprintf('%s - Web server started on %s:%d with PID %d', date('r'), $host, $port, $pid) . PHP_EOL;

// Kill the web server when the process ends
register_shutdown_function(function() use ($pid) {
        echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
        exec('kill ' . $pid);
    });

//}

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

$process = new Process("php bin/console --env=test database:scheme:update --force");
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

$mink = new Mink([
//    'zombie' => new Session(new ZombieDriver(new ZombieServer())),
//    'selenium' => new Session(new Selenium2Driver('firefox', $capabilities, $hubUri)),
    'goutte' => new Session(new GoutteDriver),
]);
$mink->setDefaultSessionName('goutte');
$mink->getSession()->setCookie('XDEBUG_SESSION', 'PHPSTORM');
